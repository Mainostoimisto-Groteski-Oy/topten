<h1>Groteskin Gutenbergiin perustuva teema</h1>

<h2>TLDR ohje projektin aloittamiseen:</h2>

1. Hae teeman paketti [demolta](https://groteskidemo.net/teemat/teema/wp-admin). Paketin mukana tulee lisäosat ja asetukset
2. Luo lokaaliin uusi sivusto, asenna sinne All-in-One WP Migration ja tuo demolta haettu paketti
3. Luo GitHubiin uusi repo käyttämällä [teema-templatea](https://github.com/Mainostoimisto-Groteski-Oy/teema).
4. Avaa app/public/wp-content/themes kansio. Jos kansiossa on teemoja, poista ne. (WP suosittelee jättämään viimeisimmän twentytwenty -teeman, jätä jos haluat)
5. Kloonaa luomasi repo tähän kansioon, esimerkiksi: `git clone https://github.com/Mainostoimisto-Groteski-Oy/REPONNIMI.git`
6. Siirry kloonaamaasi kansioon ja aja `npm install`
7. Käynnistä gulp ajamalla komento `gulp`

### Sisältö

1. [Gulp](#1-gulp)
2. [CSS](#2-css)
3. [Sass](#3-sass)
4. [JavaScript](#4-javascript)
5. [Assetit](#5-assetit)
6. [Blockit](#6-blockit)

<details>
	<summary><h2 id="gulp">1. Gulp</h2></summary>

Gulp kääntää automaagisesti sass-tiedostot sivustolle ja Gutenbergin editoriin, sekä yhdistää ja minimoi JavaScript-tiedostot.

Gulp tekee myös automaattisesti source mapit tiedostoille. Source mapeilla näkee suoraan missä .scss tai .js tiedostossa koodi on.

    gulp

Kuuntelee .scss ja .js tiedostoja ja kääntää sekä minimoi .scss-tiedostot, yhdistää ja minimoi .js-tiedostot ja optimoi assets-kansiosta löytyvät assetit.

    gulp prod

Kääntää tiedostot ilman source mappeja, tyhjentää kaikki dist-kansiot ja nostaa teeman versionumeroita yhdellä (esim. 1.0.0 -> 1.0.1). Ei kuuntele tiedostoja, joten ajaa itsensä vain kerran. Tämä kannattaa ehdottomasti ajaa ennen sivuston julkaisua.

    gulp bs

Tekee samaa kuin normaali `gulp`, mutta tässä on aktivoitu BrowserSync. BrowserSync päivittää sivun automaattisesti kun tiedostoja muokataan (scss, js, php). BrowserSync kuuntelee automaattisesti teemannimi.local osoitetta.

Jos teeman nimi ei ole sama kuin sivuston nimi, tulee gulpfilen configista muutaa bs.opts.proxy oikeaan osoitteeseen.

</details>

<details>
<summary><h2>2. CSS</h2></summary>

Käytetään CSS Gridiä

https://css-tricks.com/snippets/css/complete-guide-grid/

https://cssgridgarden.com/#fi

Jos haluaa käyttää Bootstrap-tyyliin 12-kolumnin systeemiä (leiskat on tehty tämän mukaan), kannattaa se tehdä fr-yksikön avulla.

Esimerkiksi `grid-template-columns: 1fr 10fr 1fr` on sama asia kuin `<div class="offset-1 col-10">`.

Toinen esimerkki: `grid-template-columns: 1fr 5fr 5fr 1fr;` on sama asia kuin `<div class="offset-1 col-5"></div><div class="col-5"></div>`

Jos jostain syystä kolumnien koot on väärin ja esimerkiksi `grid-template-columns: 1fr 1fr` ei ole 50% 50%, kokeile korvata fr-arvot `minmax(0, 1fr)` arvolla.

Eli esimerkiksi:

`grid-template-columns: 1fr 1fr` => `grid-template-columns: minmax(0, 1fr) minmax(0, 1fr)`

---

css/src/normalize kansiosta löytyy tiedostoja, joilla normalisoidaan CSS eri selaimien välillä.
accessiblity, box-sizing ja generic -tiedostot on luotu itse, ja niitä voi hyvillä mielin muokata.
[normalize.scss](github.com/necolas/normalize.css) on suosittu perustiedosto.

</details>

<details>
<summary><h2>3. Sass</h2></summary>

Sass-tiedostot löytyvät css/src/ kansiosta. css/src/ pääkansiosta löytyy global.scss, gutenberg.scss ja site.scss. global.scss sisältää tiedostot, joita halutaan näyttää sekä editorissa ja itse sivustolla (blockit, layout). gutenberg.scss sisältää tiedostot, jotka näytetään vain editorissa ja site.scss sisältää tiedostot, jotka näytetään vain sivustolla. Tämä jako on tehty koska joitain css-selectoreita on mukava käyttää ns. globaalisti (a.button, a.link tms.), mutta editorissa on samalla tyylillä nimettyjä asioita.

css/src/mixins kansiosta löytyy mixinit. Suositellaan vahvasti käyttämään esimerkiksi nappeja ja linkkejä mixineina juuri edellelä mainitusta syystä.

css/dist/ kansioon tulevat Gulpin pyöräyttämät tiedostot, tänne ei kannata muokkauksia tehdä.

site/typography tiedostoon on määritelty [0.1rem = 1px](https://snook.ca/archives/html_and_css/font-size-with-rem). Tämä mahdollistaa fonttikoon helpon laskemisen (64px = 6.4rem, 16px = 1.6rem, etc).

</details>

<details>
<summary><h2>4. JavaScript</h2></summary>

JavaScript tiedostot tulee olla js/src/ kansion alla.

js/dist/ kansioon tulevat Gulpin pyöräyttämät tiedostot, tänne ei kannata muokkauksia tehdä.

Jos haluat js-tiedostojen toimivan editorissa, tulee ne wrapata funktioksi, jota kutsutaan sivua ladatessa JA sen lisäksi `window.acf.addAction()`lla.
js/src/blocks/slider-block.js on esimerkki tästä.

</details>

<details>
<summary><h2>5. Assetit</h2></summary>

Kuvat ja ikonit tulee olla assets/src kansion alla. Gulp optimoi ne, ja heittää ne sen jälkeen assets/dist kansioon. Gulp tekee kuvista kaksi eri versiota, toinen on optimoitu versio alkuperäisestä kuvasta alkuperäisellä tiedostomuodolla (.png, .jpeg), ja toinen on .webp muodossa. Sassiin on tehty background -mixin, joka luo background-image propertyn .webp ja alkuperäisessä muodossa, ja lataa .webp-version jos käyttäjän selain tukee sitä (käyttää apuna modernizr-javascriptkirjastoa. Tälle mixinille annetaan arvoksi kuvan path. Variables-kansioon on myös määritetty muuttuja icons- ja images kansiolle.

Tämä tehdään sen takia, että Apple lisäsi tuen .webplle vasta äskettäin.

Esimerkkikäyttö:

`@include background( "#{$images}/koriste.png" );`

tai

`@include background( "../../assets/dist/images/koriste.png" );`

</details>

<details>
<summary><h2>6. Blockit</h2></summary>

functions.php tiedostossa on kaksi funktiota, joihin uudet blockit tulee lisätä.

`topten_allowed_block_types()` lisätään arrayhyn uusi rivi 'acf/blockin-nimi' (nimenomaan blockin nimi, slug ei toimi koska ???)
`topten_acf()` funktioon lisätään mallin mukaisesti uusi block. Teemassa on käytetty muuttujia blockin nimeämiseen, ei ole pakko käyttää jos ei halua.

</details>
