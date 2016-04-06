/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Polish language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['pl_iso-8859-5'] =
{
	/**
	 * The language reading direction. Possible values are "rtl" for
	 * Right-To-Left languages (like Arabic) and "ltr" for Left-To-Right
	 * languages (like English).
	 * @default 'ltr'
	 */
	dir : 'ltr',

	/*
	 * Screenreader titles. Please note that screenreaders are not always capable
	 * of reading non-English words. So be careful while translating it.
	 */
	editorTitle : 'Rich text editor, %1, press ALT 0 for help.', // MISSING

	// ARIA descriptions.
	toolbar	: 'Toolbar', // MISSING
	editor	: 'Rich Text Editor', // MISSING

	// Toolbar buttons without dialogs.
	source			: 'Zrodlo dokumentu',
	newPage			: 'Nowa strona',
	save			: 'Zapisz',
	preview			: 'Podglad',
	cut				: 'Wytnij',
	copy			: 'Kopiuj',
	paste			: 'Wklej',
	print			: 'Drukuj',
	underline		: 'Podkreslenie',
	bold			: 'Pogrubienie',
	italic			: 'Kursywa',
	selectAll		: 'Zaznacz wszystko',
	removeFormat	: 'Usun formatowanie',
	strike			: 'Przekreslenie',
	subscript		: 'Indeks dolny',
	superscript		: 'Indeks gorny',
	horizontalrule	: 'Wstaw pozioma linie',
	pagebreak		: 'Wstaw odstep',
	unlink			: 'Usun hiperlacze',
	undo			: 'Cofnij',
	redo			: 'Ponow',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Przegladaj',
		url				: 'Adres URL',
		protocol		: 'Protokol',
		upload			: 'Wyslij',
		uploadSubmit	: 'Wyslij',
		image			: 'Obrazek',
		flash			: 'Flash',
		form			: 'Formularz',
		checkbox		: 'Pole wyboru (checkbox)',
		radio			: 'Pole wyboru (radio)',
		textField		: 'Pole tekstowe',
		textarea		: 'Obszar tekstowy',
		hiddenField		: 'Pole ukryte',
		button			: 'Przycisk',
		select			: 'Lista wyboru',
		imageButton		: 'Przycisk-obrazek',
		notSet			: '<nie ustawione>',
		id				: 'Id',
		name			: 'Nazwa',
		langDir			: 'Kierunek tekstu',
		langDirLtr		: 'Od lewej do prawej (LTR)',
		langDirRtl		: 'Od prawej do lewej (RTL)',
		langCode		: 'Kod jezyka',
		longDescr		: 'Dlugi opis hiperlacza',
		cssClass		: 'Nazwa klasy CSS',
		advisoryTitle	: 'Opis obiektu docelowego',
		cssStyle		: 'Styl',
		ok				: 'OK',
		cancel			: 'Anuluj',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'Ogolne',
		advancedTab		: 'Zaawansowane',
		validateNumberFailed : 'Ta wartosc nie jest liczba.',
		confirmNewPage	: 'Wszystkie niezapisane zmiany zostana utracone. Czy na pewno wczytac nowa strone?',
		confirmCancel	: 'Pewne opcje zostaly zmienione. Czy na pewno zamknac okno dialogowe?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'New Window (_blank)', // MISSING
		targetTop		: 'Topmost Window (_top)', // MISSING
		targetSelf		: 'Same Window (_self)', // MISSING
		targetParent	: 'Parent Window (_parent)', // MISSING

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, niedostepne</span>'
	},

	contextmenu :
	{
		options : 'Context Menu Options' // MISSING
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Wstaw znak specjalny',
		title		: 'Wybierz znak specjalny',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Wstaw/edytuj hiperlacze',
		other 		: '<inny>',
		menu		: 'Edytuj hiperlacze',
		title		: 'Hiperlacze',
		info		: 'Informacje ',
		target		: 'Cel',
		upload		: 'Wyslij',
		advanced	: 'Zaawansowane',
		type		: 'Typ hiperlacza',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Odnosnik wewnatrz strony',
		toEmail		: 'Adres e-mail',
		targetFrame		: '<ramka>',
		targetPopup		: '<wyskakujace okno>',
		targetFrameName	: 'Nazwa Ramki Docelowej',
		targetPopupName	: 'Nazwa wyskakujacego okna',
		popupFeatures	: 'Wlasciwosci wyskakujacego okna',
		popupResizable	: 'Skalowalny',
		popupStatusBar	: 'Pasek statusu',
		popupLocationBar: 'Pasek adresu',
		popupToolbar	: 'Pasek narzedzi',
		popupMenuBar	: 'Pasek menu',
		popupFullScreen	: 'Pelny ekran (IE)',
		popupScrollBars	: 'Paski przewijania',
		popupDependent	: 'Okno zalezne (Netscape)',
		popupWidth		: 'Szerokosc',
		popupLeft		: 'Pozycja w poziomie',
		popupHeight		: 'Wysokosc',
		popupTop		: 'Pozycja w pionie',
		id				: 'Id',
		langDir			: 'Kierunek tekstu',
		langDirLTR		: 'Od lewej do prawej (LTR)',
		langDirRTL		: 'Od prawej do lewej (RTL)',
		acccessKey		: 'Klawisz dostepu',
		name			: 'Nazwa',
		langCode		: 'Kierunek tekstu',
		tabIndex		: 'Indeks tabeli',
		advisoryTitle	: 'Opis obiektu docelowego',
		advisoryContentType	: 'Typ MIME obiektu docelowego',
		cssClasses		: 'Nazwa klasy CSS',
		charset			: 'Kodowanie znakow obiektu docelowego',
		styles			: 'Styl',
		selectAnchor	: 'Wybierz etykiete',
		anchorName		: 'Wg etykiety',
		anchorId		: 'Wg identyfikatora elementu',
		emailAddress	: 'Adres e-mail',
		emailSubject	: 'Temat',
		emailBody		: 'Tresc',
		noAnchors		: '(W dokumencie nie zdefiniowano zadnych etykiet)',
		noUrl			: 'Podaj adres URL',
		noEmail			: 'Podaj adres e-mail'
		
    // wstawka Kameleona
    ,
    toInside : "Link wewnetrzny", 
		variables : "Dodatkowe zmienne",
		toPliki : "Pliki",
		toObrazki : "Obrazki",
		pliki : "Pliki"
		
		// koniec wstawka Kameleona
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Wstaw/edytuj kotwice',
		menu		: 'Wlasciwosci kotwicy',
		title		: 'Wlasciwosci kotwicy',
		name		: 'Nazwa kotwicy',
		errorName	: 'Wpisz nazwe kotwicy'
	},

	// List style dialog
	list:
	{
		numberedTitle		: 'Numbered List Properties', // MISSING
		bulletedTitle		: 'Bulleted List Properties', // MISSING
		type				: 'Type', // MISSING
		start				: 'Start', // MISSING
		circle				: 'Circle', // MISSING
		disc				: 'Disc', // MISSING
		square				: 'Square', // MISSING
		none				: 'None', // MISSING
		notset				: '<not set>', // MISSING
		armenian			: 'Armenian numbering', // MISSING
		georgian			: 'Georgian numbering (an, ban, gan, etc.)', // MISSING
		lowerRoman			: 'Lower Roman (i, ii, iii, iv, v, etc.)', // MISSING
		upperRoman			: 'Upper Roman (I, II, III, IV, V, etc.)', // MISSING
		lowerAlpha			: 'Lower Alpha (a, b, c, d, e, etc.)', // MISSING
		upperAlpha			: 'Upper Alpha (A, B, C, D, E, etc.)', // MISSING
		lowerGreek			: 'Lower Greek (alpha, beta, gamma, etc.)', // MISSING
		decimal				: 'Decimal (1, 2, 3, etc.)', // MISSING
		decimalLeadingZero	: 'Decimal leading zero (01, 02, 03, etc.)' // MISSING
	},

	// Find And Replace Dialog
	findAndReplace :
	{
		title				: 'Znajdz i zamien',
		find				: 'Znajdz',
		replace				: 'Zamien',
		findWhat			: 'Znajdz:',
		replaceWith			: 'Zastap przez:',
		notFoundMsg			: 'Nie znaleziono szukanego hasla.',
		matchCase			: 'Uwzglednij wielkosc liter',
		matchWord			: 'Cale slowa',
		matchCyclic			: 'Cykliczne dopasowanie',
		replaceAll			: 'Zastap wszystko',
		replaceSuccessMsg	: '%1 wystapien zastapionych.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabela',
		title		: 'Wlasciwosci tabeli',
		menu		: 'Wlasciwosci tabeli',
		deleteTable	: 'Usun tabele',
		rows		: 'Liczba wierszy',
		columns		: 'Liczba kolumn',
		border		: 'Grubosc ramki',
		align		: 'Wyrownanie',
		alignLeft	: 'Do lewej',
		alignCenter	: 'Do srodka',
		alignRight	: 'Do prawej',
		width		: 'Szerokosc',
		widthPx		: 'piksele',
		widthPc		: '%',
		widthUnit	: 'width unit', // MISSING
		height		: 'Wysokosc',
		cellSpace	: 'Odstep pomiedzy komorkami',
		cellPad		: 'Margines wewnetrzny komorek',
		caption		: 'Tytul',
		summary		: 'Podsumowanie',
		headers		: 'Naglowki',
		headersNone		: 'Brak',
		headersColumn	: 'Pierwsza kolumna',
		headersRow		: 'Pierwszy wiersz',
		headersBoth		: 'Oba',
		invalidRows		: 'Liczba wierszy musi byc liczba wieksza niz 0.',
		invalidCols		: 'Liczba kolumn musi byc liczba wieksza niz 0.',
		invalidBorder	: 'Liczba obramowan musi byc liczba.',
		invalidWidth	: 'Szerokosc tabeli musi byc liczba.',
		invalidHeight	: 'Wysokosc tabeli musi byc liczba.',
		invalidCellSpacing	: 'Odstep komorek musi byc liczba.',
		invalidCellPadding	: 'Dopelnienie komorek musi byc liczba.',

		cell :
		{
			menu			: 'Komorka',
			insertBefore	: 'Wstaw komorke z lewej',
			insertAfter		: 'Wstaw komorke z prawej',
			deleteCell		: 'Usun komorki',
			merge			: 'Polacz komorki',
			mergeRight		: 'Polacz z komorka z prawej',
			mergeDown		: 'Polacz z komorka ponizej',
			splitHorizontal	: 'Podziel komorke poziomo',
			splitVertical	: 'Podziel komorke pionowo',
			title			: 'Wlasciwosci komorki',
			cellType		: 'Typ komorki',
			rowSpan			: 'Scalenie wierszy',
			colSpan			: 'Scalenie komorek',
			wordWrap		: 'Zawijanie slow',
			hAlign			: 'Wyrownanie poziome',
			vAlign			: 'Wyrownanie pionowe',
			alignTop		: 'Gora',
			alignMiddle		: 'Srodek',
			alignBottom		: 'Dol',
			alignBaseline	: 'Linia bazowa',
			bgColor			: 'Kolor tla',
			borderColor		: 'Kolor obramowania',
			data			: 'Dane',
			header			: 'Naglowek',
			yes				: 'Tak',
			no				: 'Nie',
			invalidWidth	: 'Szerokosc komorki musi byc liczba.',
			invalidHeight	: 'Wysokosc komorki musi byc liczba.',
			invalidRowSpan	: 'Scalenie wierszy musi byc liczba calkowita.',
			invalidColSpan	: 'Scalenie komorek musi byc liczba calkowita.',
			chooseColor		: 'Wybierz'
		},

		row :
		{
			menu			: 'Wiersz',
			insertBefore	: 'Wstaw wiersz powyzej',
			insertAfter		: 'Wstaw wiersz ponizej',
			deleteRow		: 'Usun wiersze'
		},

		column :
		{
			menu			: 'Kolumna',
			insertBefore	: 'Wstaw kolumne z lewej',
			insertAfter		: 'Wstaw kolumne z prawej',
			deleteColumn	: 'Usun kolumny'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Wlasciwosci przycisku',
		text		: 'Tekst (Wartosc)',
		type		: 'Typ',
		typeBtn		: 'Przycisk',
		typeSbm		: 'Wyslij',
		typeRst		: 'Wyzeruj'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Wlasciwosci pola wyboru (checkbox)',
		radioTitle	: 'Wlasciwosci pola wyboru (radio)',
		value		: 'Wartosc',
		selected	: 'Zaznaczone'
	},

	// Form Dialog.
	form :
	{
		title		: 'Wlasciwosci formularza',
		menu		: 'Wlasciwosci formularza',
		action		: 'Akcja',
		method		: 'Metoda',
		encoding	: 'Kodowanie'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Wlasciwosci listy wyboru',
		selectInfo	: 'Informacje',
		opAvail		: 'Dostepne opcje',
		value		: 'Wartosc',
		size		: 'Rozmiar',
		lines		: 'linii',
		chkMulti	: 'Wielokrotny wybor',
		opText		: 'Tekst',
		opValue		: 'Wartosc',
		btnAdd		: 'Dodaj',
		btnModify	: 'Zmien',
		btnUp		: 'Do gory',
		btnDown		: 'Do dolu',
		btnSetValue : 'Ustaw wartosc zaznaczona',
		btnDelete	: 'Usun'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Wlasciwosci obszaru tekstowego',
		cols		: 'Kolumnu',
		rows		: 'Wiersze'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Wlasciwosci pola tekstowego',
		name		: 'Nazwa',
		value		: 'Wartosc',
		charWidth	: 'Szerokosc w znakach',
		maxChars	: 'Max. szerokosc',
		type		: 'Typ',
		typeText	: 'Tekst',
		typePass	: 'Haslo'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Wlasciwosci pola ukrytego',
		name	: 'Nazwa',
		value	: 'Wartosc'
	},

	// Image Dialog.
	image :
	{
		title		: 'Wlasciwosci obrazka',
		titleButton	: 'Wlasciwosci przycisku obrazka',
		menu		: 'Wlasciwosci obrazka',
		infoTab		: 'Informacje o obrazku',
		btnUpload	: 'Wyslij',
		upload		: 'Wyslij',
		alt			: 'Tekst zastepczy',
		width		: 'Szerokosc',
		height		: 'Wysokosc',
		lockRatio	: 'Zablokuj proporcje',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Przywroc rozmiar',
		border		: 'Ramka',
		hSpace		: 'Odstep poziomy',
		vSpace		: 'Odstep pionowy',
		align		: 'Wyrownaj',
		alignLeft	: 'Do lewej',
		alignRight	: 'Do prawej',
		alertUrl	: 'Podaj adres obrazka.',
		linkTab		: 'Hiperlacze',
		button2Img	: 'Czy chcesz przekonwertowac zaznaczony przycisk graficzny do zwyklego obrazka?',
		img2Button	: 'Czy chcesz przekonwertowac zaznaczony obrazek do przycisku graficznego?',
		urlMissing	: 'Podaj adres URL obrazka.',
		validateWidth	: 'Width must be a whole number.', // MISSING
		validateHeight	: 'Height must be a whole number.', // MISSING
		validateBorder	: 'Border must be a whole number.', // MISSING
		validateHSpace	: 'HSpace must be a whole number.', // MISSING
		validateVSpace	: 'VSpace must be a whole number.' // MISSING
		,zoom : 'Link do obrazka' // wstawka Kameleona
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Wlasciwosci elementu Flash',
		propertiesTab	: 'Wlasciwosci',
		title			: 'Wlasciwosci elementu Flash',
		chkPlay			: 'Autoodtwarzanie',
		chkLoop			: 'Petla',
		chkMenu			: 'Wlacz menu',
		chkFull			: 'Dopusc pelny ekran',
 		scale			: 'Skaluj',
		scaleAll		: 'Pokaz wszystko',
		scaleNoBorder	: 'Bez Ramki',
		scaleFit		: 'Dokladne dopasowanie',
		access			: 'Dostep skryptow',
		accessAlways	: 'Zawsze',
		accessSameDomain: 'Ta sama domena',
		accessNever		: 'Nigdy',
		align			: 'Wyrownaj',
		alignLeft		: 'Do lewej',
		alignAbsBottom	: 'Do dolu',
		alignAbsMiddle	: 'Do srodka w pionie',
		alignBaseline	: 'Do linii bazowej',
		alignBottom		: 'Do dolu',
		alignMiddle		: 'Do srodka',
		alignRight		: 'Do prawej',
		alignTextTop	: 'Do gory tekstu',
		alignTop		: 'Do gory',
		quality			: 'Jakosc',
		qualityBest		: 'Najlepsza',
		qualityHigh		: 'Wysoka',
		qualityAutoHigh	: 'Auto wysoka',
		qualityMedium	: 'Srednia',
		qualityAutoLow	: 'Auto niska',
		qualityLow		: 'Niska',
		windowModeWindow: 'Okno',
		windowModeOpaque: 'Nieprzezroczyste',
		windowModeTransparent : 'Przezroczyste',
		windowMode		: 'Tryb okna',
		flashvars		: 'Zmienne dla Flasha',
		bgcolor			: 'Kolor tla',
		width			: 'Szerokosc',
		height			: 'Wysokosc',
		hSpace			: 'Odstep poziomy',
		vSpace			: 'Odstep pionowy',
		validateSrc		: 'Podaj adres URL',
		validateWidth	: 'Szerokosc musi byc liczba.',
		validateHeight	: 'Wysokosc musi byc liczba.',
		validateHSpace	: 'Odstep poziomy musi byc liczba.',
		validateVSpace	: 'Odstep pionowy musi byc liczba.'
		// wstawka Kameleona
    ,url : 'Adres URL',
    preview : 'Podglad'
    // koniec wstawka Kameleona
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Sprawdz pisownie',
		title			: 'Sprawdz pisownie',
		notAvailable	: 'Przepraszamy, ale usluga jest obecnie niedostepna.',
		errorLoading	: 'Blad wczytywania hosta aplikacji uslugi: %s.',
		notInDic		: 'Slowa nie ma w slowniku',
		changeTo		: 'Zmien na',
		btnIgnore		: 'Ignoruj',
		btnIgnoreAll	: 'Ignoruj wszystkie',
		btnReplace		: 'Zmien',
		btnReplaceAll	: 'Zmien wszystkie',
		btnUndo			: 'Cofnij',
		noSuggestions	: '- Brak sugestii -',
		progress		: 'Trwa sprawdzanie...',
		noMispell		: 'Sprawdzanie zakonczone: nie znaleziono bledow',
		noChanges		: 'Sprawdzanie zakonczone: nie zmieniono zadnego slowa',
		oneChange		: 'Sprawdzanie zakonczone: zmieniono jedno slowo',
		manyChanges		: 'Sprawdzanie zakonczone: zmieniono %l slow',
		ieSpellDownload	: 'Slownik nie jest zainstalowany. Chcesz go sciagnac?'
	},

	smiley :
	{
		toolbar	: 'Emotikona',
		title	: 'Wstaw emotikone',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : 'element %1'
	},

	numberedlist	: 'Lista numerowana',
	bulletedlist	: 'Lista wypunktowana',
	indent			: 'Zwieksz wciecie',
	outdent			: 'Zmniejsz wciecie',

	justify :
	{
		left	: 'Wyrownaj do lewej',
		center	: 'Wyrownaj do srodka',
		right	: 'Wyrownaj do prawej',
		block	: 'Wyrownaj do lewej i prawej'
	},

	blockquote : 'Cytat',

	clipboard :
	{
		title		: 'Wklej',
		cutError	: 'Ustawienia bezpieczenstwa Twojej przegladarki nie pozwalaja na automatyczne wycinanie tekstu. Uzyj skrotu klawiszowego Ctrl/Cmd+X.',
		copyError	: 'Ustawienia bezpieczenstwa Twojej przegladarki nie pozwalaja na automatyczne kopiowanie tekstu. Uzyj skrotu klawiszowego Ctrl/Cmd+C.',
		pasteMsg	: 'Prosze wkleic w ponizszym polu uzywajac klawiaturowego skrotu (<STRONG>Ctrl/Cmd+V</STRONG>) i kliknac <STRONG>OK</STRONG>.',
		securityMsg	: 'Zabezpieczenia przegladarki uniemozliwiaja wklejenie danych bezposrednio do edytora. Prosze dane wkleic ponownie w tym okienku.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'Tekst, ktory chcesz wkleic, prawdopodobnie pochodzi z programu Word. Czy chcesz go wyczyscic przed wklejeniem?',
		toolbar			: 'Wklej z Worda',
		title			: 'Wklej z Worda',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'Wklej jako czysty tekst',
		title	: 'Wklej jako czysty tekst'
	},

	templates :
	{
		button			: 'Szablony',
		title			: 'Szablony zawartosci',
		options : 'Template Options', // MISSING
		insertOption	: 'Zastap aktualna zawartosc',
		selectPromptMsg	: 'Wybierz szablon do otwarcia w edytorze<br>(obecna zawartosc okna edytora zostanie utracona):',
		emptyListMsg	: '(Brak zdefiniowanych szablonow)'
	},

	showBlocks : 'Pokaz bloki',

	stylesCombo :
	{
		label		: 'Styl',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Style blokowe',
		panelTitle2	: 'Style liniowe',
		panelTitle3	: 'Style obiektowe'
	},

	format :
	{
		label		: 'Format',
		panelTitle	: 'Format',

		tag_p		: 'Normalny',
		tag_pre		: 'Tekst sformatowany',
		tag_address	: 'Adres',
		tag_h1		: 'Naglowek 1',
		tag_h2		: 'Naglowek 2',
		tag_h3		: 'Naglowek 3',
		tag_h4		: 'Naglowek 4',
		tag_h5		: 'Naglowek 5',
		tag_h6		: 'Naglowek 6',
		tag_div		: 'Normalny (DIV)'
	},

	div :
	{
		title				: 'Create Div Container', // MISSING
		toolbar				: 'Create Div Container', // MISSING
		cssClassInputLabel	: 'Stylesheet Classes', // MISSING
		styleSelectLabel	: 'Style', // MISSING
		IdInputLabel		: 'Id', // MISSING
		languageCodeInputLabel	: ' Language Code', // MISSING
		inlineStyleInputLabel	: 'Inline Style', // MISSING
		advisoryTitleInputLabel	: 'Advisory Title', // MISSING
		langDirLabel		: 'Language Direction', // MISSING
		langDirLTRLabel		: 'Left to Right (LTR)', // MISSING
		langDirRTLLabel		: 'Right to Left (RTL)', // MISSING
		edit				: 'Edit Div', // MISSING
		remove				: 'Remove Div' // MISSING
  	},

	font :
	{
		label		: 'Czcionka',
		voiceLabel	: 'Czcionka',
		panelTitle	: 'Czcionka'
	},

	fontSize :
	{
		label		: 'Rozmiar',
		voiceLabel	: 'Rozmiar czcionki',
		panelTitle	: 'Rozmiar'
	},

	colorButton :
	{
		textColorTitle	: 'Kolor tekstu',
		bgColorTitle	: 'Kolor tla',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automatycznie',
		more			: 'Wiecej kolorow...'
	},

	colors :
	{
		'000' : 'Black', // MISSING
		'800000' : 'Maroon', // MISSING
		'8B4513' : 'Saddle Brown', // MISSING
		'2F4F4F' : 'Dark Slate Gray', // MISSING
		'008080' : 'Teal', // MISSING
		'000080' : 'Navy', // MISSING
		'4B0082' : 'Indigo', // MISSING
		'696969' : 'Dim Gray', // MISSING
		'B22222' : 'Fire Brick', // MISSING
		'A52A2A' : 'Brown', // MISSING
		'DAA520' : 'Golden Rod', // MISSING
		'006400' : 'Dark Green', // MISSING
		'40E0D0' : 'Turquoise', // MISSING
		'0000CD' : 'Medium Blue', // MISSING
		'800080' : 'Purple', // MISSING
		'808080' : 'Gray', // MISSING
		'F00' : 'Red', // MISSING
		'FF8C00' : 'Dark Orange', // MISSING
		'FFD700' : 'Gold', // MISSING
		'008000' : 'Green', // MISSING
		'0FF' : 'Cyan', // MISSING
		'00F' : 'Blue', // MISSING
		'EE82EE' : 'Violet', // MISSING
		'A9A9A9' : 'Dark Gray', // MISSING
		'FFA07A' : 'Light Salmon', // MISSING
		'FFA500' : 'Orange', // MISSING
		'FFFF00' : 'Yellow', // MISSING
		'00FF00' : 'Lime', // MISSING
		'AFEEEE' : 'Pale Turquoise', // MISSING
		'ADD8E6' : 'Light Blue', // MISSING
		'DDA0DD' : 'Plum', // MISSING
		'D3D3D3' : 'Light Grey', // MISSING
		'FFF0F5' : 'Lavender Blush', // MISSING
		'FAEBD7' : 'Antique White', // MISSING
		'FFFFE0' : 'Light Yellow', // MISSING
		'F0FFF0' : 'Honeydew', // MISSING
		'F0FFFF' : 'Azure', // MISSING
		'F0F8FF' : 'Alice Blue', // MISSING
		'E6E6FA' : 'Lavender', // MISSING
		'FFF' : 'White' // MISSING
	},

	scayt :
	{
		title			: 'Sprawdz pisownie podczas pisania (SCAYT)',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'Wlacz SCAYT',
		disable			: 'Wylacz SCAYT',
		about			: 'Na temat SCAYT',
		toggle			: 'Przelacz SCAYT',
		options			: 'Opcje',
		langs			: 'Jezyki',
		moreSuggestions	: 'Wiecej sugestii',
		ignore			: 'Ignoruj',
		ignoreAll		: 'Ignoruj wszystkie',
		addWord			: 'Dodaj slowo',
		emptyDic		: 'Nazwa slownika nie moze byc pusta.',

		optionsTab		: 'Opcje',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Jezyki',

		dictionariesTab	: 'Slowniki',
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'Na temat SCAYT'
	},

	about :
	{
		title		: 'Na temat CKEditor',
		dlgTitle	: 'Na temat CKEditor',
		moreInfo	: 'Informacje na temat licencji mozna znalezc na naszej stronie:',
		copy		: 'Copyright &copy; $1. Wszelkie prawa zastrzezone.'
	},

	maximize : 'Maksymalizuj',
	minimize : 'Minimalizuj',

	fakeobjects :
	{
		anchor	: 'Kotwica',
		flash	: 'Animacja Flash',
		div		: 'Separator stron',
		unknown	: 'Nieznany obiekt'
		,maska : 'Maska' // wstawka Kameleona
	},

	resize : 'Przeciagnij, aby zmienic rozmiar',

	colordialog :
	{
		title		: 'Wybierz kolor',
		options	:	'Color Options', // MISSING
		highlight	: 'Zaznacz',
		selected	: 'Wybrany',
		clear		: 'Wyczysc'
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
	
	// wstawka Kameleona
	,maska :
	{
		toolbar		: 'Wstaw maske',
		menu		: 'Wstaw maske',
		properties : 'Wlasciwosci maski',
		title		: 'Wstaw maske',
		name		: 'Identyfikator modulu',
		errorName	: 'Wpisz identyfikator modulu'
	}
	// koniec wstawka Kameleona
};
