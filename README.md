Dokumentace pro upravenou Část:
#  Semestrální práce – Backend pro správu produktů restaurací

##  Cíl práce

Cílem bylo upravit a refaktorovat stávající backendový projekt tak, aby odpovídal principům **enterprise aplikací**. Byla vytvořena a rozšířena funkční větev pro **správu produktů administrátorem**.

---

##  Architektura systému

Aplikace je rozdělena do následujících vrstev:

- **Entity**: `ProductEntity` – reprezentace dat z databáze  
- **DTO**: `ProductDTO` – transport dat mezi vrstvami  
- **Repository**: `ProductRepository` – přístup k databázi  
- **Service**: `ProductService` – business logika, validace, logování  
- **Controller**: `ProductController` – zpracování žádostí z formuláře a API  
- **Middleware**: `AuthMiddleware` – kontrola přihlášení  
- **Utils**: `Logger`, `ProductValidator`

>  Použito čisté PHP bez frameworku, s ruční implementací **dependency injection** přes konstruktory.

---

##  Bezpečnostní mechanismy

- **JWT autentizace** – `/api/login_api.php` poskytuje token po přihlášení  
- **Session-based ochrana** – GUI (`admin_products.php`) chráněno pomocí `AuthMiddleware`  
- **Role-based logika** – připraveno pro role `admin`, `editor` dle DB struktury  

---

##  Validace a zpětná vazba

- **Vlastní validátor**: `ProductValidator`  
- Chybné vstupy vráceny jako pole a zobrazeny na frontendu  
- V API jsou odpovědi ve formátu JSON:

```json
{
  "errors": ["Musíš vybrat platnou restauraci."]
}
```

---

##  Logování a monitoring

- **Logger** (`Logger.php`) zapisuje do `backend/logs/app.log`  
- Podporuje úrovně: `info`, `warning`, `error`  
- Logují se klíčové akce: přidání, validace, mazání  
- **Zdravotní endpoint**:

```json
GET /health_check.php
{
  "status": "ok",
  "timestamp": 1749586231
}
```

---

##  Ukázka API

### POST `/api/login_api.php`

**Tělo:**

```json
{
  "username": "admin2",
  "password": "tajneheslo"
}
```

**Odpověď:**

```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### GET `/api/products?route=products`

**Hlavička:**

```
Authorization: Bearer {token}
```

---

### POST `/api/products?route=products`

**Tělo:**

```json
{
  "name": "Burger",
  "price": 149,
  "image": "burger.jpg",
  "restaurant_id": 1
}
```

---

##  Závěr

Projekt demonstruje jednu ucelenou, refaktorovanou a plně funkční větev backendové aplikace. Pokrývá:

-  Správnou architekturu (Entity, DTO, Repository, Service, Controller)
-  Bezpečnost (JWT, session)
-  Validaci a zpětnou vazbu
-  Logování a monitoring
-  Dokumentované API






Celá dokumentace:
Software Requirements Specification (SRS)
1. Úvod
1.1 Účel
Cílem tohoto dokumentu je specifikovat požadavky na webovou aplikaci pro online objednávání jídla. Dokument slouží jako referenční bod pro vývoj, testování a údržbu systému.
1.2 Rozsah
Aplikace umožní:
•	Uživatelům objednávat jídlo z restaurací.
•	Editorům (restauracím) spravovat pokrmy a objednávky.
•	Adminům řídit celý systém, schvalovat pokrmy, spravovat uživatele a restaurace.
1.3 Definice a zkratky
•	Admin – Správce systému.
•	Editor – Restaurace nebo správce jídel.
•	Uživatel – Zákazník objednávající jídlo.
________________________________________
2. Přehled systému
2.1 Funkce systému
•	Registrace, přihlášení a správa účtů.
•	Prohlížení nabídky jídel.
•	Objednávkový systém s košíkem.
•	Přehled a historie objednávek.
•	Admin funkce: schvalování jídel, správa uživatelů, restaurací, pokrmů.
•	Editor funkce: správa vlastní restaurace, jídel a objednávek.
2.2 Uživatelské charakteristiky
•	Uživatelé: Osoby různého věku, bez technické znalosti.
•	Editoři: Podnikatelé nebo zaměstnanci restaurací.
•	Admini: Technicky zdatní správci systému.
2.3 Omezení
•	Webová aplikace funguje pouze online přes prohlížeč.
•	Neexistuje mobilní nativní aplikace.
•	Aplikace pokrývá geograficky omezenou oblast.
________________________________________
3. Funkční požadavky
3.1 Uživatel
•	Registrace, přihlášení, odhlášení.
•	Prohlížení a filtrování jídel.
•	Vkládání jídel do košíku, úprava počtu porcí.
•	Objednávka včetně výběru platby a zadání adresy.
•	Zobrazení vlastních objednávek.
3.2 Editor
•	Přihlášení editora.
•	Správa své restaurace a profilu.
•	Přidávání, úprava a mazání jídel.
•	Zobrazení objednávek pro vlastní restauraci.
3.3 Admin
•	Správa všech uživatelů a jejich rolí.
•	Přidávání a mazání restaurací, přidělení editorů.
•	Schvalování pokrmů editorů.
•	Dashboard s přehledem dat.
________________________________________
4. Přehled systému (technologie)
4.1 Frontend:
•	HTML, CSS, JavaScript (ručně psané)
•	Font Awesome pro ikony
•	Jednoduché a přehledné responzivní UI
4.2 Backend:
•	PHP 8+
•	MySQL databáze
•	PDO pro bezpečnou komunikaci s DB
________________________________________
5. Nefunkční požadavky
5.1 Výkon
•	Zpracování až 1000 objednávek za hodinu.
•	Odezva serveru do 1 sekundy pro běžnou akci.
5.2 Bezpečnost
•	Šifrování hesel pomocí SHA1 (v plánu přechod na password_hash()).
•	Session-based autentizace.
•	Prepared statements (PDO) – ochrana proti SQL injection.
5.3 Použitelnost
•	Responzivní rozhraní optimalizované i pro mobilní zařízení.
•	Přehledné a jednoduché rozhraní pro všechny role.
5.4 Spolehlivost
•	Automatické přesměrování nepřihlášeného uživatele.
•	Ošetření neexistujících dat v URL.
•	Minimální výpadky díky lokálnímu běhu přes XAMPP.
________________________________________
6. Uživatelská rozhraní
6.1 Uživatelská část
•	Hlavní stránka s nabídkou jídel.
•	Košík s možností úpravy.
•	Objednávkový formulář.
•	Přehled objednávek a uživatelský panel.
6.2 Editor rozhraní
•	Dashboard restaurace.
•	Seznam jídel a formulář pro přidání.
•	Detailní stránka objednávek.
6.3 Admin rozhraní
•	Dashboard s přehledy.
•	Uživatelské účty (filtr, změna role, mazání).
•	Schvalování pokrmů, přidávání restaurací.
________________________________________
7. Business model
7.1 Cílová skupina
•	Menší podniky v ČR s vlastním rozvozem.
7.2 Plánovaný zisk
•	Zpoplatnění přístupu editorů (měsíční/ročně).
•	Možnost reklamního prostoru v systému.
7.3 Náklady
•	Fixní: hosting, údržba, doména.
•	Variabilní: vývoj, marketing.

Uživatelská a administrátorská příručka
Uživatelská příručka
Úvod
Tato příručka slouží pro koncové uživatele aplikace Dobrožrout – tedy zákazníky, kteří si prostřednictvím webu objednávají jídlo z restaurací. Popisuje základní funkce, které má uživatel k dispozici.
1. Registrace a přihlášení
Aby mohl uživatel objednávat jídlo, musí být zaregistrován.
•	Otevřete domovskou stránku aplikace.
•	Klikněte na ikonu uživatele v horním panelu nebo přejděte do sekce „Přihlásit“.
•	V případě, že nemáte účet, klikněte na možnost „Registrovat“ a vyplňte potřebné údaje.
•	Po registraci se můžete přihlásit pomocí emailu a hesla.
2. Prohlížení nabídky jídel
Po přihlášení nebo i bez přihlášení lze zobrazit seznam jídel.
•	Na hlavní stránce přejděte do sekce „Nabídka“.
•	Zobrazí se pokrmy včetně názvu, obrázku, ceny a názvu restaurace, která je nabízí.
3. Objednávání jídla
Objednávku lze vytvořit pomocí košíku.
•	Klikněte na tlačítko „Přidat do košíku“ u vybraného jídla.
•	Otevřete košík (ikona nákupního vozíku v horní liště).
•	Můžete upravit množství nebo pokrm z košíku odebrat.
•	Vyplňte dodací údaje a zvolte způsob platby.
•	Odesláním formuláře bude objednávka zaznamenána.
4. Přehled a sledování objednávek
•	Po přihlášení klikněte na ikonu objednávek (krabice).
•	Zobrazí se seznam všech vašich objednávek a aktuální stav každé z nich.
________________________________________
Editorská příručka
Úvod
Tato příručka je určena editorům – tedy zástupcům restaurací, kteří prostřednictvím administrační části spravují vlastní pokrmy a objednávky.
1. Přihlášení editora
•	Otevřete přihlašovací stránku pro uživatele.
•	Po přihlášení se systém rozpozná vaši roli editora a zobrazí editor rozhraní.
2. Dashboard restaurace
•	Po přihlášení se zobrazí přehled vaší restaurace.
•	Zde vidíte základní údaje: název, popis, počet pokrmů, počet přijatých objednávek.
3. Správa jídel
•	Přejděte do sekce „Pokrmy“.
•	Zde můžete přidávat nová jídla (název, cena, obrázek).
•	Můžete také upravovat nebo mazat dříve přidané pokrmy.
•	Všechna nová jídla musejí být schválena administrátorem, než se zobrazí veřejně.
4. Objednávky
•	V sekci „Objednávky“ vidíte seznam objednávek směřujících do vaší restaurace.
•	U každé objednávky je uveden zákazník, objednané položky, adresa a způsob platby.
5. Úprava profilu restaurace
•	V sekci „Profil restaurace“ můžete upravit název, popis a obrázek vaší restaurace.
________________________________________
Administrátorská příručka
Úvod
Tato část popisuje funkcionality dostupné administrátorům systému. Admin má plný přístup ke správě aplikace, včetně uživatelů, restaurací, pokrmů a oprávnění.
1. Přihlášení do systému
•	Otevřete přihlašovací stránku pro administrátory (admin_login.php).
•	Po zadání přihlašovacích údajů se otevře administrační panel.
2. Dashboard
•	Po přihlášení se zobrazí přehled systému – statistiky objednávek, editorů, pokrmů.
3. Správa uživatelů
•	V sekci „Uživatelé“ můžete:
o	Zobrazit všechny uživatele podle rolí.
o	Měnit roli uživatele (uživatel, editor, admin).
o	Smazat uživatele.
4. Schvalování pokrmů
•	V sekci „Schvalování jídel“ najdete nově přidané pokrmy od editorů.
•	Každý pokrm můžete:
o	Schválit (zobrazí se veřejně).
o	Smazat (pokud je nevhodný nebo duplicita).
5. Správa restaurací
•	V sekci „Restaurace“ můžete:
o	Přidávat nové restaurace (název, popis, obrázek).
o	Přiřazovat editorům jejich podniky.
6. Správa admin účtů
•	V sekci „Admini“ lze:
o	Přidat nové administrátory.
o	Upravit profil stávajících administrátorů.


