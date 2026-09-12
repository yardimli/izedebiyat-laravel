<?php
require __DIR__.'/../vendor/autoload.php';
$preview = new class('preview') extends Tests\WriterTestCase {
    public function preview(): void {
        $this->setUp();
        $this->app->instance(Illuminate\Foundation\Vite::class,new Illuminate\Foundation\Vite());
        config(['app.url'=>'http://127.0.0.1:8123']);
        Illuminate\Support\Facades\URL::forceRootUrl('http://127.0.0.1:8123');
        $user=App\Models\User::factory()->create(['name'=>'Örnek Yazar','member_type'=>1]);
        $user->forceFill(['favorites_initialized_at'=>now(),'model_selected_at'=>now(),'selected_model'=>'test/writer','favorite_models'=>['test/writer']])->save();
        $parent=App\Models\Category::create(['category_name'=>'Edebiyat','slug'=>'edebiyat']);
        $category=App\Models\Category::create(['category_name'=>'Öykü','slug'=>'oyku','parent_category_id'=>$parent->id]);
        $book=App\Writer\Models\Book::create(['user_id'=>$user->id,'title'=>'Kıyıda Bir Akşam','subtitle'=>'Bir dönüş hikâyesi','category_id'=>$category->id,'category_name'=>'Öykü','subheading'=>'Bir sahil kasabasında başlayan hikâye.','keywords_string'=>'deniz, dönüş','document'=>App\Writer\Services\ManuscriptHtml::replacement('<h1>Birinci Bölüm</h1><p>Deniz o akşam her zamankinden daha sakindi. Elif, yıllar önce ayrıldığı kasabanın sokaklarında yürürken tanıdık bir ses duydu.</p><p><em>Belki de hiçbir şey değişmemişti.</em> Kapının önündeki nar ağacı, eski günlerdeki gibi çiçek açmıştı.</p>'),'codex_types'=>['People','Places','Items']]);
        $book->entries()->create(['type'=>'People','name'=>'Elif','content'=>'Kasabaya yıllar sonra dönen yazar.','aliases'=>[]]);
        $this->actingAs($user)->withSession(['locale'=>'tr_TR']);
        $root=storage_path('app/writer-preview');if(!is_dir($root))mkdir($root,0777,true);
        file_put_contents($root.'/library.html',$this->get('/eserlerim')->assertOk()->getContent());
        file_put_contents($root.'/editor.html',$this->get('/eserlerim/'.App\Helpers\IdHasher::encode($book->id).'/duzenle')->assertOk()->getContent());
        file_put_contents($root.'/budget.html',$this->get('/yazi-atolyesi/admin/budgets')->assertOk()->getContent());
        file_put_contents($root.'/state.json',$this->getJson('/yazi-atolyesi/api/books/'.$book->id)->assertOk()->getContent());
        echo 'Prepared isolated Turkish browser fixtures.'.PHP_EOL;
        $this->tearDown();
    }
};
$preview->preview();
