<?php

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            ['Kitaplar, sessizliğin çocukları ve belleğin anneleridir.', 'Umberto Eco', null, null],
            ['Bir klasik, söyleyeceklerini söylemeyi hiç bitirmemiş kitaptır.', 'Italo Calvino', null, null],
            ['Okur ölmeden önce bin hayat yaşar.', 'George R. R. Martin', null, null],
            ['Kitapsız bir oda, ruhsuz bir beden gibidir.', 'Cicero', null, null],
            ['Sözcükler, elbette, insanlığın kullandığı en güçlü ilaçtır.', 'Rudyard Kipling', null, null],
            ['Yazmak, konuşmadan söylemenin yoludur.', 'Jules Renard', null, null],
            ['Şiir, güçlü duyguların kendiliğinden taşmasıdır.', 'William Wordsworth', null, null],
            ['Roman, elde taşınan bir aynadır.', 'Stendhal', null, null],
            ['Edebiyat, gerçeğe eklenen bir yalandır.', 'Carlos Fuentes', null, null],
            ['İyi kitapların sonu yoktur.', 'R. D. Cumming', null, null],
            ['Bir sözcükten sonra gelen doğru sözcük, yıldırımla ateşböceği arasındaki farktır.', 'Mark Twain', null, null],
            ['Yazmak, yeniden yazmaktır.', 'Ernest Hemingway', null, null],
            ['Hikâyeler, hayatın taşınabilir yurdudur.', 'Jeanette Winterson', null, null],
            ['Bir kitap, içimizdeki donmuş denize indirilmiş baltadır.', 'Franz Kafka', null, null],
            ['Dünya vardı ki kitaplarda yaşadım.', 'Orhan Pamuk', null, null],
            ['Hayal gücü, yaratılışın başlangıcıdır.', 'George Bernard Shaw', null, null],
            ['Kelimeler özgür bırakıldığında dünyayı kurar.', 'Toni Morrison', null, null],
            ['Şiir, hatırlanacak en iyi sözcüklerin en iyi düzenidir.', 'Samuel Taylor Coleridge', null, null],
            ['Okumak, başka birinin zihniyle düşünmektir.', 'Arthur Schopenhauer', null, null],
            ['Kitaplar, zamanın zincirlerini kırar.', 'Carl Sagan', null, null],
            ['Yazarın görevi, görünmeyeni görünür kılmaktır.', 'James Baldwin', null, null],
            ['Dil, varlığın evidir.', 'Martin Heidegger', null, null],
            ['Her kitap, onu okuyana yeniden doğar.', 'Alberto Manguel', null, null],
            ['Şair, görünmeyeni gören kişidir.', 'Jonathan Swift', null, null],
            ['Kurgu, hakikatin içindeki gerçeği anlatır.', 'Albert Camus', null, null],
            ['Okumayı sevenin her yerde bir evi vardır.', 'Hazel Rochman', null, null],
            ['Kütüphane, sonsuzluğun küçük bir modelidir.', 'Jorge Luis Borges', null, null],
            ['İnsan, anlattığı hikâyeler kadar yaşar.', 'Gabriel García Márquez', null, null],
            ['Sanat, gerçeği fark etmemizi sağlayan yalandır.', 'Pablo Picasso', null, null],
            ['Yazı, sesin resmidir.', 'Voltaire', null, null],
            ['Bir şiir asla bitmez, yalnızca terk edilir.', 'Paul Valéry', null, null],
            ['Kitap, cebinizde taşıdığınız bir bahçedir.', 'Çin atasözü', null, null],
            ['Bütün büyük edebiyat, iki hikâyeden biridir: yolculuk ya da yabancının gelişi.', 'Lev Tolstoy', null, null],
            ['Okumak, düşünceyi başkasının eliyle yürütmektir.', 'Jorge Luis Borges', null, null],
            ['İyi bir roman, kahramanı hakkındaki gerçeği söyler.', 'G. K. Chesterton', null, null],
            ['Sözcükler, düşüncelerimizin giysileridir.', 'Samuel Johnson', null, null],
            ['Şiir, sözcüklerle kurulan en kısa yoldur.', 'Octavio Paz', null, null],
            ['Yazmak, insanın kendi karanlığında bir kapı açmasıdır.', 'Marguerite Duras', null, null],
            ['Edebiyat, yalnız olmadığımızın kanıtıdır.', 'C. S. Lewis', null, null],
            ['Bir kitabı açmak, dünyaya açılan bir pencereyi aralamaktır.', 'Mary Schmich', null, null],
            ['Hikâye anlatmak, kaosa biçim vermektir.', 'Jean Anouilh', null, null],
            ['Yazar, evreni adlandıran kişidir.', 'Roland Barthes', null, null],
            ['Şiir, sessizliğin yankısıdır.', 'Pablo Neruda', null, null],
            ['Kitaplar aynalardır; içlerinde yalnızca kendimizi görürüz.', 'Carlos Ruiz Zafón', null, null],
            ['Bir hikâye, insan ruhuna bırakılan mektuptur.', 'Neil Gaiman', null, null],
            ['Okumak, zihnin kanatlarını açmaktır.', 'Helen Hayes', null, null],
            ['Kelimelerin bittiği yerde şiir başlar.', 'Joseph Brodsky', null, null],
            ['Romanın tek kuralı, ilginç olmasıdır.', 'Henry James', null, null],
            ['Yazmak, zamanı görünür kılar.', 'Margaret Atwood', null, null],
            ['Her okur, okuduğu kitabın ortak yazarıdır.', 'Virginia Woolf', null, null],
            ['Yaşamak için seyahat ederiz, hayatı çoğaltmak için yazarız.', 'Hans Christian Andersen', 2, 4],
            ['Bütün dünya bir sahnedir.', 'William Shakespeare', 23, 4],
            ['Kalem, zihnin dilidir.', 'Miguel de Cervantes', 23, 4],
            ['Şiir, barışın eylemidir.', 'Pablo Neruda', 21, 3],
            ['Büyük beklentiler, büyük insanları yaratır.', 'Thomas Hardy', 2, 6],
            ['Bir şairin görevi, görünmeyeni görünür kılmaktır.', 'Fernando Pessoa', 13, 6],
            ['İnsan gördüğü şeyleri şiirle yeniden kurar.', 'Jorge Luis Borges', 24, 8],
            ['İnsan, ancak sevdiği şeylerle büyür.', 'Johann Wolfgang von Goethe', 28, 8],
            ['İnsanın sırrı yalnız yaşamak değil, uğruna yaşayacağı şeyi bulmaktır.', 'Fyodor Dostoyevski', 11, 11],
            ['Acıyı dindiren iki şey vardır: dostluk ve edebiyat.', 'Simone de Beauvoir', 9, 1],
            ['Kendine ait bir oda olmadan kurmaca yazmak güçtür.', 'Virginia Woolf', 25, 1],
            ['Hayatın en büyük armağanı, özgür bir zihindir.', 'Charles Dickens', 7, 2],
            ['Bir insanın hayatı, cesareti oranında genişler ya da daralır.', 'Anaïs Nin', 21, 2],
            ['Hayat, insanın yaşadığı değil, hatırladığı ve anlatmak için nasıl hatırladığıdır.', 'Gabriel García Márquez', 6, 3],
            ['Hatalar keşfin kapılarıdır.', 'James Joyce', 16, 6],
            ['Yazmak, dünyayı değiştirme ihtimaline inanmaktır.', 'J. K. Rowling', 31, 7],
            ['Kitap, yaşamın gürültüsünde bir pusuladır.', 'Stephen King', 21, 9],
            ['Bir şehrin ruhu, anlatılan hikâyelerinde saklıdır.', 'Italo Calvino', 15, 10],
            ['Beni korkutan fırtına değil; ona alışmaktır.', 'José Saramago', 16, 11],
            ['İyi dostlar, iyi kitaplar ve uykulu bir vicdan: ideal hayat budur.', 'Mark Twain', 30, 11],
        ];

        foreach ($quotes as [$text, $author, $day, $month]) {
            Quote::query()->updateOrCreate(
                ['quote' => $text, 'author' => $author],
                ['day' => $day, 'month' => $month]
            );
        }
    }
}
