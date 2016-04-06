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
CKEDITOR.lang['pl_iso-8859-2'] =
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
	source			: '¬ród³o dokumentu',
	newPage			: 'Nowa strona',
	save			: 'Zapisz',
	preview			: 'Podgl±d',
	cut				: 'Wytnij',
	copy			: 'Kopiuj',
	paste			: 'Wklej',
	print			: 'Drukuj',
	underline		: 'Podkre¶lenie',
	bold			: 'Pogrubienie',
	italic			: 'Kursywa',
	selectAll		: 'Zaznacz wszystko',
	removeFormat	: 'Usuñ formatowanie',
	strike			: 'Przekre¶lenie',
	subscript		: 'Indeks dolny',
	superscript		: 'Indeks górny',
	horizontalrule	: 'Wstaw poziom± liniê',
	pagebreak		: 'Wstaw odstêp',
	unlink			: 'Usuñ hiper³±cze',
	undo			: 'Cofnij',
	redo			: 'Ponów',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Przegl±daj',
		url				: 'Adres URL',
		protocol		: 'Protokó³',
		upload			: 'Wy¶lij',
		uploadSubmit	: 'Wy¶lij',
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
		langCode		: 'Kod jêzyka',
		longDescr		: 'D³ugi opis hiper³±cza',
		cssClass		: 'Nazwa klasy CSS',
		advisoryTitle	: 'Opis obiektu docelowego',
		cssStyle		: 'Styl',
		ok				: 'OK',
		cancel			: 'Anuluj',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'Ogólne',
		advancedTab		: 'Zaawansowane',
		validateNumberFailed : 'Ta warto¶æ nie jest liczb±.',
		confirmNewPage	: 'Wszystkie niezapisane zmiany zostan± utracone. Czy na pewno wczytaæ now± stronê?',
		confirmCancel	: 'Pewne opcje zosta³y zmienione. Czy na pewno zamkn±æ okno dialogowe?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'New Window (_blank)', // MISSING
		targetTop		: 'Topmost Window (_top)', // MISSING
		targetSelf		: 'Same Window (_self)', // MISSING
		targetParent	: 'Parent Window (_parent)', // MISSING

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, niedostêpne</span>'
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
		toolbar		: 'Wstaw/edytuj hiper³±cze',
		other 		: '<inny>',
		menu		: 'Edytuj hiper³±cze',
		title		: 'Hiper³±cze',
		info		: 'Informacje ',
		target		: 'Cel',
		upload		: 'Wy¶lij',
		advanced	: 'Zaawansowane',
		type		: 'Typ hiper³±cza',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Odno¶nik wewn±trz strony',
		toEmail		: 'Adres e-mail',
		targetFrame		: '<ramka>',
		targetPopup		: '<wyskakuj±ce okno>',
		targetFrameName	: 'Nazwa Ramki Docelowej',
		targetPopupName	: 'Nazwa wyskakuj±cego okna',
		popupFeatures	: 'W³a¶ciwo¶ci wyskakuj±cego okna',
		popupResizable	: 'Skalowalny',
		popupStatusBar	: 'Pasek statusu',
		popupLocationBar: 'Pasek adresu',
		popupToolbar	: 'Pasek narzêdzi',
		popupMenuBar	: 'Pasek menu',
		popupFullScreen	: 'Pe³ny ekran (IE)',
		popupScrollBars	: 'Paski przewijania',
		popupDependent	: 'Okno zale¿ne (Netscape)',
		popupWidth		: 'Szeroko¶æ',
		popupLeft		: 'Pozycja w poziomie',
		popupHeight		: 'Wysoko¶æ',
		popupTop		: 'Pozycja w pionie',
		id				: 'Id',
		langDir			: 'Kierunek tekstu',
		langDirLTR		: 'Od lewej do prawej (LTR)',
		langDirRTL		: 'Od prawej do lewej (RTL)',
		acccessKey		: 'Klawisz dostêpu',
		name			: 'Nazwa',
		langCode		: 'Kierunek tekstu',
		tabIndex		: 'Indeks tabeli',
		advisoryTitle	: 'Opis obiektu docelowego',
		advisoryContentType	: 'Typ MIME obiektu docelowego',
		cssClasses		: 'Nazwa klasy CSS',
		charset			: 'Kodowanie znaków obiektu docelowego',
		styles			: 'Styl',
		selectAnchor	: 'Wybierz etykietê',
		anchorName		: 'Wg etykiety',
		anchorId		: 'Wg identyfikatora elementu',
		emailAddress	: 'Adres e-mail',
		emailSubject	: 'Temat',
		emailBody		: 'Tre¶æ',
		noAnchors		: '(W dokumencie nie zdefiniowano ¿adnych etykiet)',
		noUrl			: 'Podaj adres URL',
		noEmail			: 'Podaj adres e-mail'
		
    // wstawka Kameleona
    ,
    toInside : "Link wewnêtrzny", 
		variables : "Dodatkowe zmienne",
		toPliki : "Pliki",
		toObrazki : "Obrazki",
		pliki : "Pliki"
		
		// koniec wstawka Kameleona
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Wstaw/edytuj kotwicê',
		menu		: 'W³a¶ciwo¶ci kotwicy',
		title		: 'W³a¶ciwo¶ci kotwicy',
		name		: 'Nazwa kotwicy',
		errorName	: 'Wpisz nazwê kotwicy'
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
		title				: 'Znajd¼ i zamieñ',
		find				: 'Znajd¼',
		replace				: 'Zamieñ',
		findWhat			: 'Znajd¼:',
		replaceWith			: 'Zast±p przez:',
		notFoundMsg			: 'Nie znaleziono szukanego has³a.',
		matchCase			: 'Uwzglêdnij wielko¶æ liter',
		matchWord			: 'Ca³e s³owa',
		matchCyclic			: 'Cykliczne dopasowanie',
		replaceAll			: 'Zast±p wszystko',
		replaceSuccessMsg	: '%1 wyst±pieñ zast±pionych.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabela',
		title		: 'W³a¶ciwo¶ci tabeli',
		menu		: 'W³a¶ciwo¶ci tabeli',
		deleteTable	: 'Usuñ tabelê',
		rows		: 'Liczba wierszy',
		columns		: 'Liczba kolumn',
		border		: 'Grubo¶æ ramki',
		align		: 'Wyrównanie',
		alignLeft	: 'Do lewej',
		alignCenter	: 'Do ¶rodka',
		alignRight	: 'Do prawej',
		width		: 'Szeroko¶æ',
		widthPx		: 'piksele',
		widthPc		: '%',
		widthUnit	: 'width unit', // MISSING
		height		: 'Wysoko¶æ',
		cellSpace	: 'Odstêp pomiêdzy komórkami',
		cellPad		: 'Margines wewnêtrzny komórek',
		caption		: 'Tytu³',
		summary		: 'Podsumowanie',
		headers		: 'Nag³owki',
		headersNone		: 'Brak',
		headersColumn	: 'Pierwsza kolumna',
		headersRow		: 'Pierwszy wiersz',
		headersBoth		: 'Oba',
		invalidRows		: 'Liczba wierszy musi byæ liczb± wiêksz± ni¿ 0.',
		invalidCols		: 'Liczba kolumn musi byæ liczb± wiêksz± ni¿ 0.',
		invalidBorder	: 'Liczba obramowañ musi byæ liczb±.',
		invalidWidth	: 'Szeroko¶æ tabeli musi byæ liczb±.',
		invalidHeight	: 'Wysoko¶æ tabeli musi byæ liczb±.',
		invalidCellSpacing	: 'Odstêp komórek musi byæ liczb±.',
		invalidCellPadding	: 'Dope³nienie komórek musi byæ liczb±.',

		cell :
		{
			menu			: 'Komórka',
			insertBefore	: 'Wstaw komórkê z lewej',
			insertAfter		: 'Wstaw komórkê z prawej',
			deleteCell		: 'Usuñ komórki',
			merge			: 'Po³±cz komórki',
			mergeRight		: 'Po³±cz z komórk± z prawej',
			mergeDown		: 'Po³±cz z komórk± poni¿ej',
			splitHorizontal	: 'Podziel komórkê poziomo',
			splitVertical	: 'Podziel komórkê pionowo',
			title			: 'W³a¶ciwo¶ci komórki',
			cellType		: 'Typ komórki',
			rowSpan			: 'Scalenie wierszy',
			colSpan			: 'Scalenie komórek',
			wordWrap		: 'Zawijanie s³ów',
			hAlign			: 'Wyrównanie poziome',
			vAlign			: 'Wyrównanie pionowe',
			alignTop		: 'Góra',
			alignMiddle		: '¦rodek',
			alignBottom		: 'Dó³',
			alignBaseline	: 'Linia bazowa',
			bgColor			: 'Kolor t³a',
			borderColor		: 'Kolor obramowania',
			data			: 'Dane',
			header			: 'Nag³owek',
			yes				: 'Tak',
			no				: 'Nie',
			invalidWidth	: 'Szeroko¶æ komórki musi byæ liczb±.',
			invalidHeight	: 'Wysoko¶æ komórki musi byæ liczb±.',
			invalidRowSpan	: 'Scalenie wierszy musi byæ liczb± ca³kowit±.',
			invalidColSpan	: 'Scalenie komórek musi byæ liczb± ca³kowit±.',
			chooseColor		: 'Wybierz'
		},

		row :
		{
			menu			: 'Wiersz',
			insertBefore	: 'Wstaw wiersz powy¿ej',
			insertAfter		: 'Wstaw wiersz poni¿ej',
			deleteRow		: 'Usuñ wiersze'
		},

		column :
		{
			menu			: 'Kolumna',
			insertBefore	: 'Wstaw kolumnê z lewej',
			insertAfter		: 'Wstaw kolumnê z prawej',
			deleteColumn	: 'Usuñ kolumny'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'W³a¶ciwo¶ci przycisku',
		text		: 'Tekst (Warto¶æ)',
		type		: 'Typ',
		typeBtn		: 'Przycisk',
		typeSbm		: 'Wy¶lij',
		typeRst		: 'Wyzeruj'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'W³a¶ciwo¶ci pola wyboru (checkbox)',
		radioTitle	: 'W³a¶ciwo¶ci pola wyboru (radio)',
		value		: 'Warto¶æ',
		selected	: 'Zaznaczone'
	},

	// Form Dialog.
	form :
	{
		title		: 'W³a¶ciwo¶ci formularza',
		menu		: 'W³a¶ciwo¶ci formularza',
		action		: 'Akcja',
		method		: 'Metoda',
		encoding	: 'Kodowanie'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'W³a¶ciwo¶ci listy wyboru',
		selectInfo	: 'Informacje',
		opAvail		: 'Dostêpne opcje',
		value		: 'Warto¶æ',
		size		: 'Rozmiar',
		lines		: 'linii',
		chkMulti	: 'Wielokrotny wybór',
		opText		: 'Tekst',
		opValue		: 'Warto¶æ',
		btnAdd		: 'Dodaj',
		btnModify	: 'Zmieñ',
		btnUp		: 'Do góry',
		btnDown		: 'Do do³u',
		btnSetValue : 'Ustaw warto¶æ zaznaczon±',
		btnDelete	: 'Usuñ'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'W³a¶ciwo¶ci obszaru tekstowego',
		cols		: 'Kolumnu',
		rows		: 'Wiersze'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'W³a¶ciwo¶ci pola tekstowego',
		name		: 'Nazwa',
		value		: 'Warto¶æ',
		charWidth	: 'Szeroko¶æ w znakach',
		maxChars	: 'Max. szeroko¶æ',
		type		: 'Typ',
		typeText	: 'Tekst',
		typePass	: 'Has³o'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'W³a¶ciwo¶ci pola ukrytego',
		name	: 'Nazwa',
		value	: 'Warto¶æ'
	},

	// Image Dialog.
	image :
	{
		title		: 'W³a¶ciwo¶ci obrazka',
		titleButton	: 'W³a¶ciwo¶ci przycisku obrazka',
		menu		: 'W³a¶ciwo¶ci obrazka',
		infoTab		: 'Informacje o obrazku',
		btnUpload	: 'Wy¶lij',
		upload		: 'Wy¶lij',
		alt			: 'Tekst zastêpczy',
		width		: 'Szeroko¶æ',
		height		: 'Wysoko¶æ',
		lockRatio	: 'Zablokuj proporcje',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Przywróæ rozmiar',
		border		: 'Ramka',
		hSpace		: 'Odstêp poziomy',
		vSpace		: 'Odstêp pionowy',
		align		: 'Wyrównaj',
		alignLeft	: 'Do lewej',
		alignRight	: 'Do prawej',
		alertUrl	: 'Podaj adres obrazka.',
		linkTab		: 'Hiper³±cze',
		button2Img	: 'Czy chcesz przekonwertowaæ zaznaczony przycisk graficzny do zwyk³ego obrazka?',
		img2Button	: 'Czy chcesz przekonwertowaæ zaznaczony obrazek do przycisku graficznego?',
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
		properties		: 'W³a¶ciwo¶ci elementu Flash',
		propertiesTab	: 'W³a¶ciwo¶ci',
		title			: 'W³a¶ciwo¶ci elementu Flash',
		chkPlay			: 'Autoodtwarzanie',
		chkLoop			: 'Pêtla',
		chkMenu			: 'W³±cz menu',
		chkFull			: 'Dopu¶æ pe³ny ekran',
 		scale			: 'Skaluj',
		scaleAll		: 'Poka¿ wszystko',
		scaleNoBorder	: 'Bez Ramki',
		scaleFit		: 'Dok³adne dopasowanie',
		access			: 'Dostêp skryptów',
		accessAlways	: 'Zawsze',
		accessSameDomain: 'Ta sama domena',
		accessNever		: 'Nigdy',
		align			: 'Wyrównaj',
		alignLeft		: 'Do lewej',
		alignAbsBottom	: 'Do do³u',
		alignAbsMiddle	: 'Do ¶rodka w pionie',
		alignBaseline	: 'Do linii bazowej',
		alignBottom		: 'Do do³u',
		alignMiddle		: 'Do ¶rodka',
		alignRight		: 'Do prawej',
		alignTextTop	: 'Do góry tekstu',
		alignTop		: 'Do góry',
		quality			: 'Jako¶æ',
		qualityBest		: 'Najlepsza',
		qualityHigh		: 'Wysoka',
		qualityAutoHigh	: 'Auto wysoka',
		qualityMedium	: '¦rednia',
		qualityAutoLow	: 'Auto niska',
		qualityLow		: 'Niska',
		windowModeWindow: 'Okno',
		windowModeOpaque: 'Nieprze¼roczyste',
		windowModeTransparent : 'Prze¼roczyste',
		windowMode		: 'Tryb okna',
		flashvars		: 'Zmienne dla Flasha',
		bgcolor			: 'Kolor t³a',
		width			: 'Szeroko¶æ',
		height			: 'Wysoko¶æ',
		hSpace			: 'Odstêp poziomy',
		vSpace			: 'Odstêp pionowy',
		validateSrc		: 'Podaj adres URL',
		validateWidth	: 'Szeroko¶æ musi byæ liczb±.',
		validateHeight	: 'Wysoko¶æ musi byæ liczb±.',
		validateHSpace	: 'Odstêp poziomy musi byæ liczb±.',
		validateVSpace	: 'Odstêp pionowy musi byæ liczb±.'
		// wstawka Kameleona
    ,url : 'Adres URL',
    preview : 'Podgl±d'
    // koniec wstawka Kameleona
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Sprawd¼ pisowniê',
		title			: 'Sprawd¼ pisowniê',
		notAvailable	: 'Przepraszamy, ale us³uga jest obecnie niedostêpna.',
		errorLoading	: 'B³±d wczytywania hosta aplikacji us³ugi: %s.',
		notInDic		: 'S³owa nie ma w s³owniku',
		changeTo		: 'Zmieñ na',
		btnIgnore		: 'Ignoruj',
		btnIgnoreAll	: 'Ignoruj wszystkie',
		btnReplace		: 'Zmieñ',
		btnReplaceAll	: 'Zmieñ wszystkie',
		btnUndo			: 'Cofnij',
		noSuggestions	: '- Brak sugestii -',
		progress		: 'Trwa sprawdzanie...',
		noMispell		: 'Sprawdzanie zakoñczone: nie znaleziono b³êdów',
		noChanges		: 'Sprawdzanie zakoñczone: nie zmieniono ¿adnego s³owa',
		oneChange		: 'Sprawdzanie zakoñczone: zmieniono jedno s³owo',
		manyChanges		: 'Sprawdzanie zakoñczone: zmieniono %l s³ów',
		ieSpellDownload	: 'S³ownik nie jest zainstalowany. Chcesz go ¶ci±gn±æ?'
	},

	smiley :
	{
		toolbar	: 'Emotikona',
		title	: 'Wstaw emotikonê',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : 'element %1'
	},

	numberedlist	: 'Lista numerowana',
	bulletedlist	: 'Lista wypunktowana',
	indent			: 'Zwiêksz wciêcie',
	outdent			: 'Zmniejsz wciêcie',

	justify :
	{
		left	: 'Wyrównaj do lewej',
		center	: 'Wyrównaj do ¶rodka',
		right	: 'Wyrównaj do prawej',
		block	: 'Wyrównaj do lewej i prawej'
	},

	blockquote : 'Cytat',

	clipboard :
	{
		title		: 'Wklej',
		cutError	: 'Ustawienia bezpieczeñstwa Twojej przegl±darki nie pozwalaj± na automatyczne wycinanie tekstu. U¿yj skrótu klawiszowego Ctrl/Cmd+X.',
		copyError	: 'Ustawienia bezpieczeñstwa Twojej przegl±darki nie pozwalaj± na automatyczne kopiowanie tekstu. U¿yj skrótu klawiszowego Ctrl/Cmd+C.',
		pasteMsg	: 'Proszê wkleiæ w poni¿szym polu u¿ywaj±c klawiaturowego skrótu (<STRONG>Ctrl/Cmd+V</STRONG>) i klikn±æ <STRONG>OK</STRONG>.',
		securityMsg	: 'Zabezpieczenia przegl±darki uniemo¿liwiaj± wklejenie danych bezpo¶rednio do edytora. Proszê dane wkleiæ ponownie w tym okienku.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'Tekst, który chcesz wkleiæ, prawdopodobnie pochodzi z programu Word. Czy chcesz go wyczy¶cic przed wklejeniem?',
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
		title			: 'Szablony zawarto¶ci',
		options : 'Template Options', // MISSING
		insertOption	: 'Zast±p aktualn± zawarto¶æ',
		selectPromptMsg	: 'Wybierz szablon do otwarcia w edytorze<br>(obecna zawarto¶æ okna edytora zostanie utracona):',
		emptyListMsg	: '(Brak zdefiniowanych szablonów)'
	},

	showBlocks : 'Poka¿ bloki',

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
		tag_h1		: 'Nag³ówek 1',
		tag_h2		: 'Nag³ówek 2',
		tag_h3		: 'Nag³ówek 3',
		tag_h4		: 'Nag³ówek 4',
		tag_h5		: 'Nag³ówek 5',
		tag_h6		: 'Nag³ówek 6',
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
		bgColorTitle	: 'Kolor t³a',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automatycznie',
		more			: 'Wiêcej kolorów...'
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
		title			: 'Sprawd¼ pisowniê podczas pisania (SCAYT)',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'W³±cz SCAYT',
		disable			: 'Wy³±cz SCAYT',
		about			: 'Na temat SCAYT',
		toggle			: 'Prze³±cz SCAYT',
		options			: 'Opcje',
		langs			: 'Jêzyki',
		moreSuggestions	: 'Wiêcej sugestii',
		ignore			: 'Ignoruj',
		ignoreAll		: 'Ignoruj wszystkie',
		addWord			: 'Dodaj s³owo',
		emptyDic		: 'Nazwa s³ownika nie mo¿e byæ pusta.',

		optionsTab		: 'Opcje',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Jêzyki',

		dictionariesTab	: 'S³owniki',
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
		moreInfo	: 'Informacje na temat licencji mo¿na znale¼æ na naszej stronie:',
		copy		: 'Copyright &copy; $1. Wszelkie prawa zastrze¿one.'
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

	resize : 'Przeci±gnij, aby zmieniæ rozmiar',

	colordialog :
	{
		title		: 'Wybierz kolor',
		options	:	'Color Options', // MISSING
		highlight	: 'Zaznacz',
		selected	: 'Wybrany',
		clear		: 'Wyczy¶æ'
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
	
	// wstawka Kameleona
	,maska :
	{
		toolbar		: 'Wstaw maskê',
		menu		: 'Wstaw maskê',
		properties : 'W³a¶ciwo¶ci maski',
		title		: 'Wstaw maskê',
		name		: 'Identyfikator modu³u',
		errorName	: 'Wpisz identyfikator modu³u'
	}
	// koniec wstawka Kameleona
};
