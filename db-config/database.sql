CREATE DATABASE streetnoirdb;

USE streetnoirdb;

CREATE TABLE utente_carrello(
    id_u int,
    id_car int,
    nome varchar(20),
    cognome varchar(20),
    email varchar(255),
    password varchar(100),
    data_c date
);

CREATE TABLE utenti(
    id_u int primary key,
    nome varchar(20),
    cognome varchar(20),
    email varchar(255),
    password varchar(100)
);

CREATE TABLE recensioni (
    id_r int primary key,
    id_u int,
    commento varchar(200),
    rating ENUM('1', '2', '3', '4', '5'),
    data_r date,
    foreign key (id_u) references utenti(id_u)
);

CREATE TABLE ordini (
    id_o int primary key,
    id_u int,
    indirizzo varchar(50),
    data_o date,
    citta varchar(50),
    cap char(5),
    metodo_pag enum('Paypal', 'Carta di Credito', 'Bitcoin', 'Alla consegna'),
    foreign key (id_u) references utenti(id_u)
);

CREATE TABLE brand (
  nome_b varchar(20) primary key
);

CREATE TABLE categorie (
    nome_cat varchar(20) primary key
);

CREATE TABLE stili (
  nome_s varchar(20) primary key
);

CREATE TABLE prodotti (
    id_p int primary key,
    nome_p varchar(20),
    descrizione varchar(100),
    prezzo decimal(10, 2),
    url_img text,
    taglia varchar(4),
    nome_b varchar(20),
    nome_cat varchar(20),
    nome_s varchar(20),
    foreign key (nome_b) references brand(nome_b),
    foreign key (nome_cat) references categorie(nome_cat),
    foreign key (nome_s) references stili(nome_s)
);

CREATE TABLE wishlist (
    id_w int primary key,
    nome_w varchar(20),
    data_w date,
    id_u int,
    id_p int,
    foreign key (id_u) references  utenti(id_u),
    foreign key (id_p) references prodotti(id_p)
);

CREATE TABLE carrelli (
    id_car int primary key,
    data_c date,
    id_p int,
    foreign key (id_p) references prodotti(id_p)
);


CREATE TABLE admin (
    id_a int primary key,
    username varchar(10),
    password varchar(100)
);

CREATE TABLE fornitori (
    id_f int primary key,
    nome varchar(20),
    indirizzo varchar(100),
    citta varchar(50),
    mail varchar(255),
    cap char(5)
);

CREATE TABLE inventari (
    id_i int primary key,
    id_p int,
    data_rif date,
    id_a int,
    id_f int,
    foreign key (id_p) references prodotti(id_p),
    foreign key (id_a) references admin(id_a),
    foreign key (id_f) references fornitori(id_f)
);

CREATE TABLE inserire (
    id_p int,
    id_w int,
    foreign key (id_p) references prodotti(id_p),
    foreign key (id_w) references wishlist(id_w),
    primary key (id_p, id_w)
);

CREATE TABLE giudicare (
    id_r int,
    id_p int,
    foreign key (id_r) references recensioni(id_r),
    foreign key (id_p) references prodotti(id_p),
    primary key (id_r, id_p)
);

CREATE TABLE acquistare (
  id_o int,
  id_p int,
  quantita int,
  foreign key (id_o) references ordini(id_o),
  foreign key (id_p) references prodotti(id_p),
  primary key (id_o, id_p)
);


INSERT INTO brand (nome_b) VALUES
('Nike'),
('Adidas'),
('Salomon'),
('New Balance'),
('Balenciaga');

INSERT INTO brand (nome_b) VALUES
('Supreme'),
('Off-White'),
('Stone Island'),
('Palm Angels'),
('Rick Owens'),
('Fear of God'),
('Under Armour'),
('ASICS'),
('Reebok'),
('Converse'),
('BAPE'),
('Nike ACG'),
('Maison Margiela'),
('Diesel'),
('Prada'),
('Gucci'),
('Versace'),
('Moncler'),
('The North Face'),
('Carhartt WIP');

INSERT INTO brand (nome_b) VALUES
('Armani'),
('Dolce & Gabbana'),
('Ermenegildo Zegna'),
('Loro Piana'),
('Tom Ford'),
('Brunello Cucinelli'),
('Canali'),
('Corneliani'),
('Boglioli'),
('Etro'),
('Kiton'),
('Isaia'),
('Hugo Boss'),
('Givenchy'),
('Celine'),
('Valentino'),
('Lanvin'),
('Saint Laurent'),
('Alexander McQueen'),
('Paul Smith');

INSERT INTO brand (nome_b) VALUES
('Ralph Lauren'),
('Lacoste'),
('Tommy Hilfiger'),
('Brooks Brothers'),
('Gant'),
('Hackett'),
('Barbour'),
('Fred Perry'),
('Ben Sherman'),
('Scotch & Soda'),
('Club Monaco'),
('Massimo Dutti'),
('J.Crew'),
('A.P.C.'),
('Theory'),
('Uniqlo U'),
('COS'),
('Ted Baker'),
('Reiss'),
('Sandro');

INSERT INTO brand (nome_b) VALUES
('Gallery Dept.'),
('Alyx'),
('Amiri'),
('Yoon Ahn (AMBUSH)'),
('Kith'),
('Maison Kitsune'),
('1017 ALYX 9SM'),
('Heron Preston'),
('John Elliott'),
('Chrome Hearts'),
('Rick Owens DRKSHDW'),
('Pyer Moss'),
('Sacai'),
('Undercover'),
('Neighborhood'),
('VETEMENTS'),
('Cav Empt');

INSERT INTO brand (nome_b) VALUES
('Levi\'s'),
('Lee'),
('Dickies'),
('Timberland'),
('Red Wing'),
('Dr. Martens'),
('Wrangler'),
('Dockers'),
('Evisu'),
('Stussy'),
('Champion'),
('Calvin Klein Jeans'),
('Lucky Brand'),
('True Religion'),
('7 For All Mankind'),
('American Eagle');

INSERT INTO brand (nome_b) VALUES
('Ray-Ban'),
('Oakley'),
('Persol'),
('Maui Jim'),
('Gucci Eyewear'),
('Prada Eyewear'),
('Versace Eyewear'),
('Tom Ford Eyewear'),
('Carrera'),
('Oliver Peoples'),
('Mykita'),
('Celine Eyewear'),
('Miu Miu Eyewear'),
('Saint Laurent E'),
('Chloé Eyewear'),
('Bvlgari Eyewear'),
('Marc Jacobs Eyewear'),
('Dior Eyewear'),
('Fendi Eyewear'),
('Burberry Eyewear');

INSERT INTO categorie (nome_cat) VALUES
('Occhiali'),
('Cappellini'),
('Magliette'),
('Felpe'),
('Giacche'),
('Gonne'),
('Pantaloni'),
('Scarpe'),
('Cinture'),
('Borse');

INSERT INTO stili (nome_s) VALUES
('Sportivo'),
('Streetwear'),
('Casual'),
('Luxury'),
('Elegante'),
('Tecnico');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
(1, 'Air Max 97', 'Sneakers iconiche con design ondulato e unità Air visibile', 180.00, 'https://mistereseller.com/cdn/shop/files/Air-Max-97-Silver-Bullet-mistereseller-DM0028-002.jpg?v=1740138073', 'Nike', 'Scarpe', 'Sportivo', '40'),
(2, 'Yeezy Boost 350 V2', 'Sneakers in Primeknit con suola Boost', 220.00, 'https://data.bigsouproma.com/images/galleries/1353/adidas-yeezy-yeezy-boost-350-v2-static_13582630_43310508_2.jpeg', 'Adidas', 'Scarpe', 'Streetwear', '37'),
(3, 'XT-6', 'Scarpe da trail running con design tecnico', 160.00, 'https://cdn-images.farfetch-contents.com/25/73/70/77/25737077_55825776_1000.jpg', 'Salomon', 'Scarpe', 'Sportivo', '39'),
(4, '990v5', 'Sneakers classiche con ammortizzazione ENCAP', 175.00, 'https://d2mn3puas3vzda.cloudfront.net/img/p/7/3/2/6/0/73260-yx_def.jpg', 'New Balance', 'Scarpe', 'Casual', '36'),
(5, 'Triple S', 'Sneakers oversize multistrato in pelle e mesh', 895.00, 'https://clothify-it.com/cdn/shop/files/Balenciaga-Triple-S-Black-White-Red-2018-Reissue-Product.webp?v=1711742214', 'Balenciaga', 'Scarpe', 'Luxury', '44'),
(6, 'Handball Spezial', 'Sneakers iconiche scamosciate', 120.00, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cbf96f70d8b54fb7a91c22b8d5edd07d_9366/Scarpe_Handball_Spezial_Marrone_IF6490_01_standard.jpg', 'Adidas', 'Scarpe', 'Casual', '40');


INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
(7, 'Ray-Ban RB8125', 'Occhiali da sole aviator in titanio con finitura oro anticato e lenti grigie classiche', 180.00, 'https://avvenice.com/it/occhiali-da-sole/10632-ray-ban-rb8125-913757-original-aviator-titanium-oro-anticato-lente-grigio-classica-occhiali-da-sole-ray-ban-eyewear.html', 'Ray-Ban', 'Occhiali', 'Luxury', 'One'),
(8, 'Oakley Gibston XL', 'Occhiali da sole sportivi con montatura nera opaca e lenti prizm', 128.00, 'https://www.motoblouz.it/occhiali-da-sole-oakley-straightlink-matte-black-lenti-prizm-109685-v.html', 'Oakley', 'Occhiali', 'Sportivo', 'One'),
(9, 'Persol 2446-S', 'Occhiali da sole in metallo stile vintage con lenti verdi', 220.00, 'https://www.giglio.com/accessori-uomo_occhiali-ponte-20-asta-145-persol-2446-s.html?cSel=012', 'Persol', 'Occhiali', 'Elegante', 'One'),
(10, 'Maui Jim Waiwai', 'Occhiali da sole aviator polarizzati in titanio', 225.00, 'https://avvenice.com/it/occhiali-da-sole/52727-maui-jim-waiwai-nero-grigio-occhiali-da-sole-aviator-polarizzati-maui-jim-eyewear.html', 'Maui Jim', 'Occhiali', 'Sportivo', 'One'),
(11, 'Gucci Occhiali', 'Occhiali da sole oversize con montatura in acetato verde, nero e rosso con glitter', 390.00, 'https://avvenice.com/it/occhiali-da-sole/9534-gucci-occhiali-da-sole-quadrati-in-acetato-verde-nero-e-rosso-con-glitter-gucci-eyewear.html', 'Gucci', 'Occhiali', 'Luxury', 'One'),
(12, 'Miu Miu Occhiali', 'Occhiali da sole con lenti sfumate e montatura in acetato, disponibili in diverse varianti di colore', 430.00, 'https://www.miumiu.com/it/it/accessories/eyewear/c/10267EU', 'Miu Miu Eyewear', 'Occhiali', 'Luxury', 'One'),
(13, 'Chloé Aly', 'Occhiali da sole con montatura in metallo e lenti a tripla sfumatura', 470.00, 'https://www.chloe.com/it/chloe/shop-online/donna/occhiali-da-sole', 'Chloé Eyewear', 'Occhiali', 'Elegante', 'One');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
-- Streetwear
(14, 'T-Shirt Logo', 'T-shirt classica con logo Gallery Dept. stampato sul petto', 180.00, 'https://cdn-images.farfetch-contents.com/17/32/42/13/17324213_36483697_600.jpg', 'Gallery Dept.', 'Magliette', 'Streetwear', 'M'),
(15, 'Palm Angels Flame', 'Maglietta oversize con stampa fiamme sul retro', 240.00, 'https://www.palmangels.com/11/11737907rl_14_f.jpg', 'Palm Angels', 'Magliette', 'Streetwear', 'L'),
(16, 'Off-White Diag Tee', 'T-shirt bianca con frecce diagonali stampate', 290.00, 'https://cdn-images.farfetch-contents.com/13/16/24/55/13162455_14213526_600.jpg', 'Off-White', 'Magliette', 'Streetwear', 'S'),

-- Eleganti
(17, 'Polo Ralph Lauren', 'Polo in cotone con logo ricamato', 110.00, 'https://cdn-images.farfetch-contents.com/12/83/20/04/12832004_14048653_600.jpg', 'Ralph Lauren', 'Magliette', 'Elegante', 'XL'),
(18, 'Lacoste Classic Fit', 'Maglietta in piqué con logo coccodrillo', 85.00, 'https://cdn.lacoste.com/lacoste/men/apparel/TH2042/001/th2042_001_01.jpg', 'Lacoste', 'Magliette', 'Elegante', 'XS'),

-- Casual
(19, 'Levi\'s Housemark Tee', 'T-shirt in cotone con logo Levi\'s stampato', 30.00, 'https://lsco.scene7.com/is/image/lsco/177830113-front-pdp.jpg', 'Levi\'s', 'Magliette', 'Casual', 'M'),
(20, 'Carhartt Pocket Tee', 'T-shirt robusta con taschino sul petto', 45.00, 'https://cdn.carhartt-wip.com/products/I022091_89_00_F-web.jpg', 'Carhartt WIP', 'Magliette', 'Casual', 's'),

-- Luxury
(21, 'Gucci Logo Tee', 'T-shirt in jersey con logo vintage Gucci', 480.00, 'https://media.gucci.com/style/DarkGray_Center_0_0_490x490/1579639803/548334_X5L26_9095_001_100_0000_Light.jpg', 'Gucci', 'Magliette', 'Luxury', 'XS'),
(22, 'Political Tee', 'Oversize t-shirt con stampa ispirata alla politica', 550.00, 'https://cdn-images.farfetch-contents.com/13/84/35/56/13843556_17022687_600.jpg', 'Balenciaga', 'Magliette', 'Luxury', 'M'),

-- Sportive
(23, 'Nike Club Tee', 'T-shirt basic in jersey con logo Nike', 35.00, 'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/e18809a1-d215-4f09-85d7-dfae3e98d3d6/sportswear-club-t-shirt-in-jersy-DVD8vn.png', 'Nike', 'Magliette', 'Sportivo', 'XL'),
(24, 'Adidas Tee', 'Maglietta con logo Adidas per uso quotidiano', 28.00, 'https://assets.adidas.com/images/w_600,f_auto,q_auto/cfc0a51028c745f5bd56ac8700c383e4_9366/Essentials_Logo_T-Shirt_Bianco_HC0822_01_laydown.jpg', 'Adidas', 'Magliette', 'Sportivo', 'L');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
-- Streetwear
(25, 'Pantaloni Track', 'Pantaloni sportivi con logo ricamato sul lato', 395.00, 'https://www.palmangels.com/11/11737907rl_14_f.jpg', 'Palm Angels', 'Pantaloni', 'Streetwear', 'M'),
(26, 'Pantaloni Flame', 'Pantaloni con stampa fiamme e logo Palm Angels', 345.00, 'https://www.palmangels.com/11/11737907rl_14_f.jpg', 'Palm Angels', 'Pantaloni', 'Streetwear', 'L'),

-- Eleganti
(27, 'Pantaloni Perf', 'Pantaloni eleganti in twill elasticizzato', 189.00, 'https://www.ralphlauren.it/11/11737907rl_14_f.jpg', 'Ralph Lauren', 'Pantaloni', 'Elegante', '40'),
(28, 'Pantaloni scozzesi', 'Pantaloni in tessuto Harris Tweed con motivo scozzese', 549.00, 'https://www.ralphlauren.it/11/11737907rl_14_f.jpg', 'Ralph Lauren', 'Pantaloni', 'Elegante', '42'),

-- Casual
(29, 'Levi\'s 501 Original', 'Jeans classici a gamba dritta', 98.00, 'https://www.levi.com/11/11737907rl_14_f.jpg', 'Levi\'s', 'Pantaloni', 'Casual', 'W32L32'),
(30, 'Carhartt WIP Pant', 'Pantaloni casual in tela resistente', 89.00, 'https://www.carhartt-wip.com/11/11737907rl_14_f.jpg', 'Carhartt WIP', 'Pantaloni', 'Casual', 'M'),

-- Luxury
(31, 'Pantaloni in velluto', 'Pantaloni eleganti in velluto a coste', 395.00, 'https://www.ralphlauren.it/11/11737907rl_14_f.jpg', 'Ralph Lauren', 'Pantaloni', 'Luxury', '38'),
(32, 'Pantaloni Gregory', 'Pantaloni in lino di alta qualità fatti a mano', 690.00, 'https://www.ralphlauren.it/11/11737907rl_14_f.jpg', 'Ralph Lauren', 'Pantaloni', 'Luxury', '40'),

-- Sportivo
(33, 'Nike Sportswear', 'Pantaloni sportivi in fleece con logo Nike', 55.00, 'https://www.nike.com/11/11737907rl_14_f.jpg', 'Nike', 'Pantaloni', 'Sportivo', 'L'),
(34, 'Adidas 3-Stripes', 'Pantaloni sportivi con le iconiche 3 strisce Adidas', 50.00, 'https://www.adidas.com/11/11737907rl_14_f.jpg', 'Adidas', 'Pantaloni', 'Sportivo', 'M');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
-- Streetwear
(35, 'BAPE Shark Tee', 'Iconica t-shirt BAPE con stampa Shark frontale', 120.00, 'https://d3nt9em9l1urz8.cloudfront.net/media/catalog/product/cache/3/image/9df78eab33525d08d6e5fb8d27136e95/a/p/ap1i30109022pur-1.jpg', 'BAPE', 'Magliette', 'Streetwear', 'L'),
(36, 'Heron Bird Tee', 'T-shirt con grafica airone e logo arancione', 200.00, 'https://images.stockx.com/images/Heron-Preston-Heron-Print-Oversized-T-Shirt-Black-Blue-Multi.jpg?fit=fill&bg=FFFFFF&w=700&h=500&fm=webp&auto=compress&q=90&dpr=2&trim=color&updated_at=1655899168', 'Heron Preston', 'Magliette', 'Streetwear', 'M'),
(37, 'Kith Logo Tee', 'Maglietta basic con logo Kith ricamato sul petto', 85.00, 'https://ca.kith.com/cdn/shop/files/KHW030215-101-FRONT.jpg?v=1694644175&width=1920', 'Kith', 'Magliette', 'Streetwear', 'S'),

-- Luxury
(38, 'Amiri Logo Tee', 'T-shirt premium con logo Amiri stampato', 330.00, 'https://d141zx60z515qt.cloudfront.net/mp02172a6560/pr22881/img13633_1200w.webp', 'Amiri', 'Magliette', 'Luxury', 'M'),
(39, 'Celine Tee', 'T-shirt in jersey con stampa Celine Paris', 390.00, 'https://www.celine.com/on/demandware.static/-/Sites-masterCatalog/default/dw3f581db4/images/large/2X04O671Q.25AE_1_FW24_P2_W.jpg', 'Celine', 'Magliette', 'Luxury', 'S'),
(40, 'Lanvin Logo Tee', 'Maglietta in cotone con logo Lanvin', 250.00, 'https://cdn-img.poizonapp.com/pro-img/cut-img/20250308/11665414724d4f4e8ce2414fdb043c5f.jpg?x-oss-process=image/format,webp/resize,w_350', 'Lanvin', 'Magliette', 'Luxury', 'L'),
(41, 'Numbers Tee', 'T-shirt con stampa numerica iconica Margiela', 290.00, 'https://cdn-images.farfetch-contents.com/22/84/26/48/22842648_52886084_1000.jpg', 'Maison Margiela', 'Magliette', 'Luxury', 'M'),

-- Casual / Trendy
(42, 'Diesel Logo Tee', 'Maglietta regular fit con logo Diesel in evidenza', 95.00, 'https://divo.dam.gogemini.io/6615a57c38cf9bb24af40b80.jpg?f=a', 'Diesel', 'Magliette', 'Casual', 'XL');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
-- Eleganti / Luxury
(43, 'Loro Piana Hoodie', 'Felpa con cappuccio in puro cashmere, eleganza e comfort', 1450.00, 'https://media.loropiana.com/HYBRIS/FAO/FAO5407/64647C01-97F3-4B11-84C2-B0F8A7C203D9/FAO5407_L0BT_LARGE.jpg', 'Loro Piana', 'Felpe', 'Elegante', 'M'),
(44, 'Alyx Hoodie', 'Felpa con grafica stampata e dettagli industriali', 480.00, 'https://cncpts.com/cdn/shop/files/ALYX_PrintedLogotreatedHoodie_Black_AAUSW0175FA02_01.jpg?v=1684875871', '1017 ALYX 9SM', 'Felpe', 'Streetwear', 'L'),
(45, 'J.Crew Hoodie', 'Felpa basic con cuori stampati', 90.00, 'https://www.jcrew.com/s7-img-facade/AE935_SU3586?hei=2000&crop=0,0,1600,0', 'J.Crew', 'Felpe', 'Casual', 'S'),
(46, 'Hugo Boss Hoodie', 'Felpa moderna con logo tono su tono', 150.00, 'https://bluebellsboutique.co.uk/cdn/shop/files/C3B24107-4E4C-45C2-AC1C-81B29D63D030.jpg?v=1726315023', 'Hugo Boss', 'Felpe', 'Elegante', 'M'),
(47, 'Paul Smith Hoodie', 'Felpa con cappuccio e fiore iconico', 220.00, 'https://www.odsdesignerclothing.com/cdn/shop/files/M2R-960X-NP4826-79__UR_10_1800x1800.jpg?v=1720706484', 'Paul Smith', 'Felpe', 'Casual', 'L'),
(48, 'Rick Owens Hoodie', 'Oversized hoodie in stile industriale', 620.00, 'https://assets.solesense.com/en/images/products/500/rick-owens-hoodie-black-ru01d3248-ba-m6_1.jpg', 'Rick Owens', 'Felpe', 'Luxury', 'XL'),

-- Casual / Streetwear
(49, 'Scotch & Soda Hoodie', 'Felpa con stampa esotica Scotch & Soda', 130.00, 'https://image-resizing.booztcdn.com/scotch-and-soda/sas178174_ceveningblack.webp?has_grey=1&has_webp=1&dpr=2.5&size=w400', 'Scotch & Soda', 'Felpe', 'Casual', 'M'),
(50, 'Stone Island Hoodie', 'Felpa con patch iconica sulla manica', 370.00, 'https://data.bigsouproma.com/images/galleries/2575/stone-island-supreme.jpeg', 'Stone Island', 'Felpe', 'Streetwear', 'L'),
(51, 'Stüssy Hoodie', 'Felpa classica con logo Stüssy e corona', 130.00, 'https://cdn-images.farfetch-contents.com/27/62/82/25/27628225_57730415_600.jpg', 'Stussy', 'Felpe', 'Streetwear', 'S'),
(52, 'Vetements Hoodie', 'Felpa oversize con grafiche ironiche', 750.00, 'https://server.spin4spin.com/images/2000000359991/2000000359991-19de1546f30878eea82e1969ce990941.jpg', 'VETEMENTS', 'Felpe', 'Luxury', 'One'),
(53, 'Versace Hoodie', 'Felpa con stampa Medusa in strass', 650.00, 'https://img01.ztat.net/article/spp-media-p1/04be1aa00cad4a399bf84e8c671c8712/066f3fdd623c4b58a34823ecf3388a7e.jpg?imwidth=1800&filter=packshot', 'Versace', 'Felpe', 'Luxury', 'M'),
(54, 'Uniqlo Crewneck', 'Felpa essenziale dallo stile minimal', 40.00, 'https://down-my.img.susercontent.com/file/sg-11134201-7rdwc-lyhd2674whdz67', 'Uniqlo U', 'Felpe', 'Casual', 'XL');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
-- Eleganti / Luxury
(55, 'Loro Piana Skirt', 'Gonna lunga in lino leggero', 980.00, 'https://images.vestiairecollective.com/images/resized/w=1246,q=75,f=auto,/produit/gonne-loro-piana-marina-cachemire-40411625-1_2.jpg', 'Loro Piana', 'Gonne', 'Elegante', '38'),
(56, 'Versace Skirt', 'Mini gonna con stampa barocca iconica', 650.00, 'https://www.versace.com/dw/image/v2/BGWN_PRD/on/demandware.static/-/Sites-ver-master-catalog/default/dw844c0657/original/90_1004222-1A03068_5B000_10_Barocco~Pleated~Skirt--Versace-online-store_2_0.jpg?sw=850&q=85&strip=true', 'Versace', 'Gonne', 'Luxury', 'S'),
(57, 'Rick Owens Skirt', 'Gonna asimmetrica con taglio destrutturato', 720.00, 'https://www.allotmentstore.com/cdn/shop/files/DSC_0647.jpg?v=1723461438', 'Rick Owens', 'Gonne', 'Luxury', 'M'),

-- Casual / Streetwear
(58, 'Stüssy Skirt', 'Gonna in denim con tasche cargo', 110.00, 'https://media-photos.depop.com/b1/25579587/2046048924_2ffcb147eaee4ade95110c8aff7ffdc2/P0.jpg', 'Stussy', 'Gonne', 'Streetwear', 'S'),
(59, 'Stone Island Skirt', 'Gonna tecnica con tessuto idrorepellente', 210.00, 'https://cms.brnstc.de/product_images/287x393_retina/cpro/media/images/product/24/7/100164082513000_0_1720426029897.jpg', 'Stone Island', 'Gonne', 'Streetwear', 'M'),
(60, 'J.Crew Skirt', 'Gonna a trapezio in cotone leggero', 75.00, 'https://messinahembry.com/cdn/shop/files/0c261fe4-36a0-4c46-9788-ad77dca65dfe.jpg?v=1704337184', 'J.Crew', 'Gonne', 'Casual', 'L'),

-- Chic contemporaneo
(61, 'Paul Smith Skirt', 'Gonna plissettata con stampa floreale moderna', 230.00, 'https://www.julesb.co.uk/stylefile/wp-content/uploads/2023/05/marble-print-skirt.jpg', 'Paul Smith', 'Gonne', 'Elegante', '38'),
(62, 'Scotch & Soda Skirt', 'Gonna midi a fantasia con elastico in vita', 140.00, 'https://cdn-images.farfetch-contents.com/23/09/58/11/23095811_53224298_300.jpg', 'Scotch & Soda', 'Gonne', 'Casual', '40'),
(63, 'Uniqlo U Skirt', 'Gonna minimal con taglio dritto', 39.90, 'https://image.uniqlo.com/UQ/ST3/WesternCommon/imagesgoods/474958/sub/goods_474958_sub14_3x4.jpg?width=600', 'Uniqlo U', 'Gonne', 'Casual', 'S'),

-- Avant-garde
(64, 'ALYX Skirt', 'Gonna ispirata al techwear con fibbie industriali', 420.00, 'https://cdn-images.italist.com/image/upload/t_medium_mobile_dpr_2_q_auto_v_2,f_auto/b8e16126446556a7c7afa7bfa4a291ff.jpg', '1017 ALYX 9SM', 'Gonne', 'Streetwear', 'M');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
(65, 'Loro Piana Cap', 'Cappellino in puro cashmere con tecnologia Storm System®', 520.00, 'https://www.mytheresa.com/media/1094/1238/100/e2/P00585791.jpg', 'Loro Piana', 'Cappellini', 'Elegante', 'One'),
(66, 'Rick Owens Cap', 'Cappellino in velluto a coste con logo ricamato', 310.00, 'https://static.ftshp.digital/img/p/1/4/1/1/4/0/5/1411405-full_product.jpg', 'Rick Owens', 'Cappellini', 'Luxury', 'One'),
(67, 'Scotch & Soda Cap', 'Cappellino in velluto a coste con logo ricamato', 45.00, 'https://scotch-soda.eu/cdn/shop/files/SSV7-2308-001-1.png?v=1743692589', 'Scotch & Soda', 'Cappellini', 'Casual', 'One'),
(68, 'Stone Island Cap', 'Cappellino tecnico in Gore-Tex con logo riflettente', 150.00, 'https://uomoclub.it/articoli/013866/2576d3526d2be11e6e065fb033436e6c46baa5ee.jpg', 'Stone Island', 'Cappellini', 'Streetwear', 'One');

INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, nome_b, nome_cat, nome_s, taglia) VALUES
-- Eleganti / Luxury
(69, 'Loro Piana Belt', 'Cintura in suede con fibbia in metallo spazzolato', 620.00, 'https://media.loropiana.com/HYBRIS/FAI/FAI1156/58A790B9-3C76-45B6-89AC-66D8DA9BBC23/FAI1156_R0CM_MEDIUM.jpg', 'Loro Piana', 'Cinture', 'Elegante', '80'),
(70, 'Versace Belt', 'Cintura in pelle con fibbia Medusa dorata', 450.00, 'https://media.max-boutique.com/catalog/product/cache/8f16e3e69bcfe9abb884a77e1642b31f/d/c/dcu4747-dvtp1k-153-nero_versace-accessori-uomo-1.jpg', 'Versace', 'Cinture', 'Luxury', '110'),
(71, 'Rick Owens Belt', 'Cintura larga in pelle con chiusura a lingua', 710.00, 'https://media.sivasdescalzo.com/media/catalog/product/R/A/RA02D0580-LGE-09_sivasdescalzo-Rick_Owens-LEATHER_BELT_-_TONGUE_BELT-1727449074-1.jpg?width=3840&optimize=high', 'Rick Owens', 'Cinture', 'Luxury', '90'),

-- Streetwear
(72, 'Alyx Belt', 'Cintura nera con fibbia industriale ispirata alle cinture di sicurezza', 310.00, 'https://image.goxip.com/KGppymOQQoXmXVlMHHf4JG9dpLw=/fit-in/500x500/filters:format(jpg):quality(80):fill(white)/https:%2F%2Fcdn.shopify.com%2Fs%2Ffiles%2F1%2F0272%2F3941%2F5891%2Ffiles%2FAAUBT0086LE01-A-marais.com.au.jpg', '1017 ALYX 9SM', 'Cinture', 'Streetwear', '120'),
(73, 'Stone Island Belt', 'Cintura in tessuto tecnico con chiusura a clip', 120.00, 'https://www.annameglio.com/img/schede/48524-stone_island_cintura_tape_stone_island_verd-2.jpg', 'Stone Island', 'Cinture', 'Streetwear', '100'),
(74, 'Stüssy Belt', 'Cintura in tela con logo stampato e fibbia metallica', 60.00, 'https://eu.stussy.com/cdn/shop/products/135187_BLAC_1.jpg?v=1698773894', 'Stussy', 'Cinture', 'Streetwear', '110'),

-- Casual
(75, 'Diesel Belt', 'Cintura classica in pelle con Logo D', 140.00, 'https://cms.brnstc.de/product_images/680x930_retina/cpro/media/images/product/23/8/100150137713000_1_1692363663600.jpg', 'Diesel', 'Cinture', 'Casual', '85'),
(76, 'Uniqlo Belt', 'Cintura essenziale in pelle sintetica con finiture minimal', 29.90, 'https://image.uniqlo.com/UQ/ST3/WesternCommon/imagesgoods/433767/item/goods_38_433767_3x4.jpg?width=494', 'Uniqlo U', 'Cinture', 'Casual', '70');




