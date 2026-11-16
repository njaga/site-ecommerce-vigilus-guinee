<?php

namespace Tests\Feature;

use App\Models\TrainingRequest;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    private function sessionData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Session de test Dahua', 'description' => 'Programme fictif pour les tests.',
            'starts_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'location' => 'Conakry — lieu de test', 'audience' => 'Installateurs', 'is_published' => true,
        ], $overrides);
    }

    private function inquiry(array $overrides = []): array
    {
        return array_merge([
            'request_type' => 'entreprise', 'company' => 'Entreprise de test',
            'name' => 'Contact de test', 'email' => 'formation@example.test', 'phone' => '+224 610 000 001',
            'participants' => 8, 'preferred_period' => 'Janvier', 'message' => 'Besoins fictifs de test.',
            'consent' => '1', 'website' => '',
        ], $overrides);
    }

    public function test_calendar_exposes_only_published_future_sessions_and_empty_state(): void
    {
        $this->get(route('training.index'))->assertOk()->assertSee('Le prochain calendrier se prépare.');
        $public = TrainingSession::create($this->sessionData());
        TrainingSession::create($this->sessionData(['title' => 'Brouillon privé', 'is_published' => false]));
        TrainingSession::create($this->sessionData(['title' => 'Session terminée', 'starts_at' => now()->subDay()]));
        $this->get(route('training.index', ['session' => $public->id]))->assertOk()
            ->assertSee($public->title)->assertSee('selected', false)
            ->assertDontSee('Brouillon privé')->assertDontSee('Session terminée')
            ->assertSee('noindex,follow');
    }

    public function test_company_request_is_persisted_and_not_publicly_disclosed(): void
    {
        $this->post(route('training.store'), $this->inquiry(['status' => 'closed']))
            ->assertRedirect(route('training.index').'#demande')->assertSessionHas('success');
        $saved = TrainingRequest::firstOrFail();
        $this->assertSame('entreprise', $saved->request_type);
        $this->assertSame(8, $saved->participants);
        $this->assertSame('new', $saved->status);
        $this->assertNotNull($saved->consented_at);
        $this->get(route('training.index'))->assertDontSee('formation@example.test');
        $this->get(route('admin.training.index'))->assertRedirect(route('login'));
    }

    public function test_request_validation_requires_company_consent_and_rejects_spam(): void
    {
        $this->post(route('training.store'), $this->inquiry(['company' => '', 'consent' => '', 'participants' => 0, 'website' => 'https://example.test']))
            ->assertSessionHasErrors(['company', 'consent', 'participants', 'website']);
        $this->assertDatabaseCount('training_requests', 0);
    }

    public function test_session_request_rejects_unpublished_or_past_sessions(): void
    {
        $session = TrainingSession::create($this->sessionData(['is_published' => false]));
        $data = $this->inquiry(['request_type' => 'session', 'training_session_id' => $session->id, 'company' => null]);
        $this->post(route('training.store'), $data)->assertSessionHasErrors('training_session_id');
        $session->update(['is_published' => true, 'starts_at' => now()->subDay()]);
        $this->post(route('training.store'), $data)->assertSessionHasErrors('training_session_id');
        $session->update(['starts_at' => now()->addDay()]);
        $this->post(route('training.store'), $data)->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertDatabaseHas('training_requests', ['training_session_id' => $session->id, 'session_title' => $session->title]);
    }

    public function test_admin_can_create_update_and_unpublish_sessions_and_follow_requests(): void
    {
        $user = User::create(['name' => 'Formation test', 'email' => 'admin-training@example.test', 'password' => 'password-long-test-123', 'is_admin' => false]);
        $this->actingAs($user)->post(route('admin.training.store'), $this->sessionData())->assertForbidden();
        $user->update(['is_admin' => true]);
        $this->actingAs($user)->get(route('admin.training.create'))->assertOk();
        $this->post(route('admin.training.store'), $this->sessionData())->assertRedirect(route('admin.training.index'));
        $session = TrainingSession::firstOrFail();
        $this->get(route('admin.training.edit', $session))->assertOk()->assertSee($session->title);
        $this->put(route('admin.training.update', $session), $this->sessionData(['is_published' => false]))->assertRedirect();
        $this->get(route('training.index'))->assertDontSee($session->title);
        $this->post(route('training.store'), $this->inquiry())->assertRedirect();
        $inquiry = TrainingRequest::firstOrFail();
        $this->get(route('admin.training.index'))->assertOk()->assertSee($inquiry->email);
        $this->patch(route('admin.training.requests.update', $inquiry), ['status' => 'contacted'])->assertRedirect();
        $this->assertSame('contacted', $inquiry->fresh()->status);
        $this->patch(route('admin.training.requests.update', $inquiry), ['status' => 'invalid'])->assertSessionHasErrors('status');
    }

    public function test_brand_assets_network_links_and_certification_details(): void
    {
        $this->get(route('about'))->assertOk()
            ->assertSee('images/Dahua_Technology_logo.svg.png')
            ->assertSee('images/certifications/iso-9001.png')
            ->assertSee('images/certifications/iso-14001.png')
            ->assertSee('images/certifications/iso-45001.png')
            ->assertSee('images/certifications/iso-29001.png')
            ->assertSee('Le représentant officiel exclusif')
            ->assertDontSee('Bientôt, le représentant')
            ->assertDontSee('Émirats arabes unis')
            ->assertDontSee('Filiale de Vigilus Groupe SA')
            ->assertSee('https://groupevigilus.ci')->assertSee('https://vigilus-facilities.sl')
            ->assertSee('https://vigilus-properties.sn')->assertSee('https://locationvoituredakar.com');
        config()->set('seo.default_indexable', true);
        $this->get(route('sitemap'))->assertOk()->assertSee('/formations');
    }
}
