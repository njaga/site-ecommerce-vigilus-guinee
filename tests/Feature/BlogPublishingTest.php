<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogPublishingTest extends TestCase
{
    use RefreshDatabase;

    private function editor(bool $admin = true): User
    {
        return User::create([
            'name' => $admin ? 'Rédacteur habilité' : 'Visiteur test',
            'email' => $admin ? 'redaction@example.test' : 'visiteur-blog@example.test',
            'password' => 'test-password-2026-long',
            'is_admin' => $admin,
        ]);
    }

    private function payload(string $status = 'draft'): array
    {
        return [
            'title' => 'Comment choisir sa vidéosurveillance à Conakry',
            'category' => 'Conseils',
            'excerpt' => 'Les principaux critères à comparer pour sélectionner des équipements adaptés à votre site.',
            'body' => str_repeat(
                'Avant de choisir une caméra, vérifiez le champ à couvrir et les contraintes techniques. ',
                7
            ),
            'cover_alt' => 'Équipement de vidéosurveillance sur fond clair',
            'seo_title' => 'Choisir sa vidéosurveillance à Conakry | Vigilus Guinée',
            'seo_description' => 'Guide des principaux critères pour sélectionner ses caméras et enregistreurs à Conakry.',
            'status' => $status,
            'is_featured' => '1',
        ];
    }

    public function test_old_public_url_is_now_a_clean_editorial_journal(): void
    {
        $this->get(route('solution.videosurveillance'))->assertOk()
            ->assertSee('Le journal Vigilus')
            ->assertSee('Actualités')
            ->assertDontSee('journal-feature-grid', false)
            ->assertSee('Le journal arrive bientôt.')
            ->assertDontSee('Photographies du produit publiées');
    }

    public function test_admin_blog_routes_are_protected_from_guests_and_non_admins(): void
    {
        $this->get(route('admin.blog.index'))->assertRedirect(route('login'));
        $this->get(route('admin.blog.create'))->assertRedirect(route('login'));
        $this->post(route('admin.blog.store'), $this->payload())
            ->assertRedirect(route('login'));

        $this->actingAs($this->editor(false));
        $this->get(route('admin.blog.index'))->assertForbidden();
        $this->get(route('admin.blog.create'))->assertForbidden();
        $this->post(route('admin.blog.store'), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('blog_posts', 0);
    }

    public function test_admin_can_create_a_draft_and_preview_it_privately(): void
    {
        $this->actingAs($this->editor());
        $this->post(route('admin.blog.store'), $this->payload())
            ->assertRedirect();

        $post = BlogPost::firstOrFail();
        $this->assertSame('draft', $post->status);
        $this->assertSame('comment-choisir-sa-videosurveillance-a-conakry', $post->slug);
        $this->get(route('blog.show', $post->slug))->assertNotFound();
        $this->get(route('solution.videosurveillance'))->assertDontSee($post->title);
        $this->get(route('admin.blog.preview', $post))->assertOk()
            ->assertSee($post->title)
            ->assertSee('noindex,nofollow')
            ->assertSee('Aperçu réservé à l’administration');
    }

    public function test_publication_requires_a_cover_photo_and_alt_text(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $this->post(route('admin.blog.store'), $this->payload('published'))
            ->assertSessionHasErrors('cover_image');
        $this->assertDatabaseCount('blog_posts', 0);

        $data = $this->payload('published');
        unset($data['cover_alt']);
        $data['cover_image'] = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $this->post(route('admin.blog.store'), $data)
            ->assertSessionHasErrors('cover_alt');
        $this->assertDatabaseCount('blog_posts', 0);
    }

    public function test_admin_can_publish_a_covered_article_and_it_appears_in_blog_and_sitemap(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $data = $this->payload('published');
        $data['cover_image'] = UploadedFile::fake()->image('cameras.jpg', 1100, 700);

        $this->post(route('admin.blog.store'), $data)->assertRedirect();
        $post = BlogPost::firstOrFail();

        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        Storage::disk('public')->assertExists($post->cover_path);

        $this->get(route('solution.videosurveillance'))->assertOk()
            ->assertSee($post->title)
            ->assertSee('journal-card-featured', false)
            ->assertSee('storage/'.$post->cover_path);

        $this->get(route('blog.show', $post->slug))->assertOk()
            ->assertSee($post->title)
            ->assertSee('BlogPosting')
            ->assertSee('Comment choisir sa vidéosurveillance')
            ->assertSee('journal-article-body', false)
            ->assertSee('Équipement de vidéosurveillance sur fond clair');

        config()->set('seo.default_indexable', true);
        $this->get(route('sitemap'))->assertOk()
            ->assertSee('/blog/'.$post->slug);
    }

    public function test_scheduled_article_cannot_be_read_before_publication_date(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());

        $data = $this->payload('published');
        $data['cover_image'] = UploadedFile::fake()->image('scheduled.jpg', 800, 600);
        $data['published_at'] = now()->addDays(2)->format('Y-m-d H:i:s');
        $this->post(route('admin.blog.store'), $data)->assertRedirect();

        $post = BlogPost::firstOrFail();
        $this->get(route('blog.show', $post->slug))->assertNotFound();
        $this->get(route('solution.videosurveillance'))->assertDontSee($post->title);

        config()->set('seo.default_indexable', true);
        $this->get(route('sitemap'))->assertDontSee('/blog/'.$post->slug);

        $this->travel(3)->days();
        $this->get(route('blog.show', $post->slug))->assertOk();
        $this->get(route('sitemap'))->assertSee('/blog/'.$post->slug);
        $this->travelBack();
    }

    public function test_article_slug_stays_stable_when_title_changes(): void
    {
        $this->actingAs($this->editor());
        $this->post(route('admin.blog.store'), $this->payload())->assertRedirect();
        $post = BlogPost::firstOrFail();
        $slug = $post->slug;

        $data = $this->payload();
        $data['title'] = 'Autre titre éditorial plus précis et détaillé';
        $this->put(route('admin.blog.update', $post), $data)->assertRedirect();

        $this->assertSame($slug, $post->fresh()->slug);
        $this->assertSame($data['title'], $post->fresh()->title);
    }

    public function test_unpublishing_removes_the_article_from_the_public_site(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $data = $this->payload('published');
        $data['cover_image'] = UploadedFile::fake()->image('article.jpg', 1000, 640);
        $this->post(route('admin.blog.store'), $data)->assertRedirect();

        $post = BlogPost::firstOrFail();
        $this->patch(route('admin.blog.archive', $post))->assertRedirect();
        $this->assertSame('draft', $post->fresh()->status);
        $this->get(route('blog.show', $post->slug))->assertNotFound();
        $this->get(route('solution.videosurveillance'))->assertDontSee($post->title);

        config()->set('seo.default_indexable', true);
        $this->get(route('sitemap'))->assertDontSee('/blog/'.$post->slug);
    }

    public function test_published_article_cannot_be_destroyed_even_after_unpublishing(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $data = $this->payload('published');
        $data['cover_image'] = UploadedFile::fake()->image('article.jpg', 850, 560);
        $this->post(route('admin.blog.store'), $data)->assertRedirect();

        $post = BlogPost::firstOrFail();
        $this->delete(route('admin.blog.destroy', $post))->assertSessionHasErrors('article');
        $this->patch(route('admin.blog.archive', $post))->assertRedirect();
        $this->delete(route('admin.blog.destroy', $post))->assertSessionHasErrors('article');
        $this->assertDatabaseCount('blog_posts', 1);
    }

    public function test_only_never_published_drafts_can_be_deleted(): void
    {
        $this->actingAs($this->editor());
        $this->post(route('admin.blog.store'), $this->payload())->assertRedirect();
        $post = BlogPost::firstOrFail();

        $this->delete(route('admin.blog.destroy', $post))
            ->assertRedirect(route('admin.blog.index'));
        $this->assertDatabaseCount('blog_posts', 0);
    }

    public function test_replacing_cover_image_removes_previous_file(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $data = $this->payload('published');
        $data['cover_image'] = UploadedFile::fake()->image('first.jpg', 850, 560);
        $this->post(route('admin.blog.store'), $data)->assertRedirect();
        $post = BlogPost::firstOrFail();
        $old = $post->cover_path;

        $update = $this->payload('published');
        $update['cover_image'] = UploadedFile::fake()->image('second.jpg', 850, 560);
        $this->put(route('admin.blog.update', $post), $update)->assertRedirect();

        $new = $post->fresh()->cover_path;
        $this->assertNotEquals($old, $new);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($new);
    }

    public function test_markdown_removes_raw_html_in_article_body(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $data = $this->payload('published');
        $data['cover_image'] = UploadedFile::fake()->image('article.jpg', 850, 560);
        $data['body'] .= "\n\n<script>alert('xss')</script>\n\n## Conseils pratiques\n\n[Lien douteux](javascript:alert(1))";
        $this->post(route('admin.blog.store'), $data)->assertRedirect();

        $post = BlogPost::firstOrFail();
        $this->get(route('blog.show', $post->slug))->assertOk()
            ->assertSee('Conseils pratiques')
            ->assertDontSee('<script>', false)
            ->assertDontSee('href="javascript:', false);
    }

    public function test_validation_rejects_short_articles_and_fake_file_extensions(): void
    {
        Storage::fake('public');
        $this->actingAs($this->editor());
        $data = $this->payload('published');
        $data['body'] = 'Trop court';
        $data['cover_image'] = UploadedFile::fake()->create('script.php', 2, 'text/plain');

        $this->post(route('admin.blog.store'), $data)
            ->assertSessionHasErrors(['body', 'cover_image']);
        $this->assertDatabaseCount('blog_posts', 0);
    }
}
