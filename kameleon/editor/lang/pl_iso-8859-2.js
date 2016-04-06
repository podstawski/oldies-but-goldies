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
	source			: '�r�d�o dokumentu',
	newPage			: 'Nowa strona',
	save			: 'Zapisz',
	preview			: 'Podgl�d',
	cut				: 'Wytnij',
	copy			: 'Kopiuj',
	paste			: 'Wklej',
	print			: 'Drukuj',
	underline		: 'Podkre�lenie',
	bold			: 'Pogrubienie',
	italic			: 'Kursywa',
	selectAll		: 'Zaznacz wszystko',
	removeFormat	: 'Usu� formatowanie',
	strike			: 'Przekre�lenie',
	subscript		: 'Indeks dolny',
	superscript		: 'Indeks g�rny',
	horizontalrule	: 'Wstaw poziom� lini�',
	pagebreak		: 'Wstaw odst�p',
	unlink			: 'Usu� hiper��cze',
	undo			: 'Cofnij',
	redo			: 'Pon�w',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Przegl�daj',
		url				: 'Adres URL',
		protocol		: 'Protok�',
		upload			: 'Wy�lij',
		uploadSubmit	: 'Wy�lij',
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
		langCode		: 'Kod j�zyka',
		longDescr		: 'D�ugi opis hiper��cza',
		cssClass		: 'Nazwa klasy CSS',
		advisoryTitle	: 'Opis obiektu docelowego',
		cssStyle		: 'Styl',
		ok				: 'OK',
		cancel			: 'Anuluj',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'Og�lne',
		advancedTab		: 'Zaawansowane',
		validateNumberFailed : 'Ta warto�� nie jest liczb�.',
		confirmNewPage	: 'Wszystkie niezapisane zmiany zostan� utracone. Czy na pewno wczyta� now� stron�?',
		confirmCancel	: 'Pewne opcje zosta�y zmienione. Czy na pewno zamkn�� okno dialogowe?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'New Window (_blank)', // MISSING
		targetTop		: 'Topmost Window (_top)', // MISSING
		targetSelf		: 'Same Window (_self)', // MISSING
		targetParent	: 'Parent Window (_parent)', // MISSING

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, niedost�pne</span>'
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
		toolbar		: 'Wstaw/edytuj hiper��cze',
		other 		: '<inny>',
		menu		: 'Edytuj hiper��cze',
		title		: 'Hiper��cze',
		info		: 'Informacje ',
		target		: 'Cel',
		upload		: 'Wy�lij',
		advanced	: 'Zaawansowane',
		type		: 'Typ hiper��cza',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Odno�nik wewn�trz strony',
		toEmail		: 'Adres e-mail',
		targetFrame		: '<ramka>',
		targetPopup		: '<wyskakuj�ce okno>',
		targetFrameName	: 'Nazwa Ramki Docelowej',
		targetPopupName	: 'Nazwa wyskakuj�cego okna',
		popupFeatures	: 'W�a�ciwo�ci wyskakuj�cego okna',
		popupResizable	: 'Skalowalny',
		popupStatusBar	: 'Pasek statusu',
		popupLocationBar: 'Pasek adresu',
		popupToolbar	: 'Pasek narz�dzi',
		popupMenuBar	: 'Pasek menu',
		popupFullScreen	: 'Pe�ny ekran (IE)',
		popupScrollBars	: 'Paski przewijania',
		popupDependent	: 'Okno zale�ne (Netscape)',
		popupWidth		: 'Szeroko��',
		popupLeft		: 'Pozycja w poziomie',
		popupHeight		: 'Wysoko��',
		popupTop		: 'Pozycja w pionie',
		id				: 'Id',
		langDir			: 'Kierunek tekstu',
		langDirLTR		: 'Od lewej do prawej (LTR)',
		langDirRTL		: 'Od prawej do lewej (RTL)',
		acccessKey		: 'Klawisz dost�pu',
		name			: 'Nazwa',
		langCode		: 'Kierunek tekstu',
		tabIndex		: 'Indeks tabeli',
		advisoryTitle	: 'Opis obiektu docelowego',
		advisoryContentType	: 'Typ MIME obiektu docelowego',
		cssClasses		: 'Nazwa klasy CSS',
		charset			: 'Kodowanie znak�w obiektu docelowego',
		styles			: 'Styl',
		selectAnchor	: 'Wybierz etykiet�',
		anchorName		: 'Wg etykiety',
		anchorId		: 'Wg identyfikatora elementu',
		emailAddress	: 'Adres e-mail',
		emailSubject	: 'Temat',
		emailBody		: 'Tre��',
		noAnchors		: '(W dokumencie nie zdefiniowano �adnych etykiet)',
		noUrl			: 'Podaj adres URL',
		noEmail			: 'Podaj adres e-mail'
		
    // wstawka Kameleona
    ,
    toInside : "Link wewn�trzny", 
		variables : "Dodatkowe zmienne",
		toPliki : "Pliki",
		toObrazki : "Obrazki",
		pliki : "Pliki"
		
		// koniec wstawka Kameleona
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Wstaw/edytuj kotwic�',
		menu		: 'W�a�ciwo�ci kotwicy',
		title		: 'W�a�ciwo�ci kotwicy',
		name		: 'Nazwa kotwicy',
		errorName	: 'Wpisz nazw� kotwicy'
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
		title				: 'Znajd� i zamie�',
		find				: 'Znajd�',
		replace				: 'Zamie�',
		findWhat			: 'Znajd�:',
		replaceWith			: 'Zast�p przez:',
		notFoundMsg			: 'Nie znaleziono szukanego has�a.',
		matchCase			: 'Uwzgl�dnij wielko�� liter',
		matchWord			: 'Ca�e s�owa',
		matchCyclic			: 'Cykliczne dopasowanie',
		replaceAll			: 'Zast�p wszystko',
		replaceSuccessMsg	: '%1 wyst�pie� zast�pionych.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabela',
		title		: 'W�a�ciwo�ci tabeli',
		menu		: 'W�a�ciwo�ci tabeli',
		deleteTable	: 'Usu� tabel�',
		rows		: 'Liczba wierszy',
		columns		: 'Liczba kolumn',
		border		: 'Grubo�� ramki',
		align		: 'Wyr�wnanie',
		alignLeft	: 'Do lewej',
		alignCenter	: 'Do �rodka',
		alignRight	: 'Do prawej',
		width		: 'Szeroko��',
		widthPx		: 'piksele',
		widthPc		: '%',
		widthUnit	: 'width unit', // MISSING
		height		: 'Wysoko��',
		cellSpace	: 'Odst�p pomi�dzy kom�rkami',
		cellPad		: 'Margines wewn�trzny kom�rek',
		caption		: 'Tytu�',
		summary		: 'Podsumowanie',
		headers		: 'Nag�owki',
		headersNone		: 'Brak',
		headersColumn	: 'Pierwsza kolumna',
		headersRow		: 'Pierwszy wiersz',
		headersBoth		: 'Oba',
		invalidRows		: 'Liczba wierszy musi by� liczb� wi�ksz� ni� 0.',
		invalidCols		: 'Liczba kolumn musi by� liczb� wi�ksz� ni� 0.',
		invalidBorder	: 'Liczba obramowa� musi by� liczb�.',
		invalidWidth	: 'Szeroko�� tabeli musi by� liczb�.',
		invalidHeight	: 'Wysoko�� tabeli musi by� liczb�.',
		invalidCellSpacing	: 'Odst�p kom�rek musi by� liczb�.',
		invalidCellPadding	: 'Dope�nienie kom�rek musi by� liczb�.',

		cell :
		{
			menu			: 'Kom�rka',
			insertBefore	: 'Wstaw kom�rk� z lewej',
			insertAfter		: 'Wstaw kom�rk� z prawej',
			deleteCell		: 'Usu� kom�rki',
			merge			: 'Po��cz kom�rki',
			mergeRight		: 'Po��cz z kom�rk� z prawej',
			mergeDown		: 'Po��cz z kom�rk� poni�ej',
			splitHorizontal	: 'Podziel kom�rk� poziomo',
			splitVertical	: 'Podziel kom�rk� pionowo',
			title			: 'W�a�ciwo�ci kom�rki',
			cellType		: 'Typ kom�rki',
			rowSpan			: 'Scalenie wierszy',
			colSpan			: 'Scalenie kom�rek',
			wordWrap		: 'Zawijanie s��w',
			hAlign			: 'Wyr�wnanie poziome',
			vAlign			: 'Wyr�wnanie pionowe',
			alignTop		: 'G�ra',
			alignMiddle		: '�rodek',
			alignBottom		: 'D�',
			alignBaseline	: 'Linia bazowa',
			bgColor			: 'Kolor t�a',
			borderColor		: 'Kolor obramowania',
			data			: 'Dane',
			header			: 'Nag�owek',
			yes				: 'Tak',
			no				: 'Nie',
			invalidWidth	: 'Szeroko�� kom�rki musi by� liczb�.',
			invalidHeight	: 'Wysoko�� kom�rki musi by� liczb�.',
			invalidRowSpan	: 'Scalenie wierszy musi by� liczb� ca�kowit�.',
			invalidColSpan	: 'Scalenie kom�rek musi by� liczb� ca�kowit�.',
			chooseColor		: 'Wybierz'
		},

		row :
		{
			menu			: 'Wiersz',
			insertBefore	: 'Wstaw wiersz powy�ej',
			insertAfter		: 'Wstaw wiersz poni�ej',
			deleteRow		: 'Usu� wiersze'
		},

		column :
		{
			menu			: 'Kolumna',
			insertBefore	: 'Wstaw kolumn� z lewej',
			insertAfter		: 'Wstaw kolumn� z prawej',
			deleteColumn	: 'Usu� kolumny'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'W�a�ciwo�ci przycisku',
		text		: 'Tekst (Warto��)',
		type		: 'Typ',
		typeBtn		: 'Przycisk',
		typeSbm		: 'Wy�lij',
		typeRst		: 'Wyzeruj'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'W�a�ciwo�ci pola wyboru (checkbox)',
		radioTitle	: 'W�a�ciwo�ci pola wyboru (radio)',
		value		: 'Warto��',
		selected	: 'Zaznaczone'
	},

	// Form Dialog.
	form :
	{
		title		: 'W�a�ciwo�ci formularza',
		menu		: 'W�a�ciwo�ci formularza',
		action		: 'Akcja',
		method		: 'Metoda',
		encoding	: 'Kodowanie'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'W�a�ciwo�ci listy wyboru',
		selectInfo	: 'Informacje',
		opAvail		: 'Dost�pne opcje',
		value		: 'Warto��',
		size		: 'Rozmiar',
		lines		: 'linii',
		chkMulti	: 'Wielokrotny wyb�r',
		opText		: 'Tekst',
		opValue		: 'Warto��',
		btnAdd		: 'Dodaj',
		btnModify	: 'Zmie�',
		btnUp		: 'Do g�ry',
		btnDown		: 'Do do�u',
		btnSetValue : 'Ustaw warto�� zaznaczon�',
		btnDelete	: 'Usu�'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'W�a�ciwo�ci obszaru tekstowego',
		cols		: 'Kolumnu',
		rows		: 'Wiersze'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'W�a�ciwo�ci pola tekstowego',
		name		: 'Nazwa',
		value		: 'Warto��',
		charWidth	: 'Szeroko�� w znakach',
		maxChars	: 'Max. szeroko��',
		type		: 'Typ',
		typeText	: 'Tekst',
		typePass	: 'Has�o'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'W�a�ciwo�ci pola ukrytego',
		name	: 'Nazwa',
		value	: 'Warto��'
	},

	// Image Dialog.
	image :
	{
		title		: 'W�a�ciwo�ci obrazka',
		titleButton	: 'W�a�ciwo�ci przycisku obrazka',
		menu		: 'W�a�ciwo�ci obrazka',
		infoTab		: 'Informacje o obrazku',
		btnUpload	: 'Wy�lij',
		upload		: 'Wy�lij',
		alt			: 'Tekst zast�pczy',
		width		: 'Szeroko��',
		height		: 'Wysoko��',
		lockRatio	: 'Zablokuj proporcje',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Przywr�� rozmiar',
		border		: 'Ramka',
		hSpace		: 'Odst�p poziomy',
		vSpace		: 'Odst�p pionowy',
		align		: 'Wyr�wnaj',
		alignLeft	: 'Do lewej',
		alignRight	: 'Do prawej',
		alertUrl	: 'Podaj adres obrazka.',
		linkTab		: 'Hiper��cze',
		button2Img	: 'Czy chcesz przekonwertowa� zaznaczony przycisk graficzny do zwyk�ego obrazka?',
		img2Button	: 'Czy chcesz przekonwertowa� zaznaczony obrazek do przycisku graficznego?',
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
		properties		: 'W�a�ciwo�ci elementu Flash',
		propertiesTab	: 'W�a�ciwo�ci',
		title			: 'W�a�ciwo�ci elementu Flash',
		chkPlay			: 'Autoodtwarzanie',
		chkLoop			: 'P�tla',
		chkMenu			: 'W��cz menu',
		chkFull			: 'Dopu�� pe�ny ekran',
 		scale			: 'Skaluj',
		scaleAll		: 'Poka� wszystko',
		scaleNoBorder	: 'Bez Ramki',
		scaleFit		: 'Dok�adne dopasowanie',
		access			: 'Dost�p skrypt�w',
		accessAlways	: 'Zawsze',
		accessSameDomain: 'Ta sama domena',
		accessNever		: 'Nigdy',
		align			: 'Wyr�wnaj',
		alignLeft		: 'Do lewej',
		alignAbsBottom	: 'Do do�u',
		alignAbsMiddle	: 'Do �rodka w pionie',
		alignBaseline	: 'Do linii bazowej',
		alignBottom		: 'Do do�u',
		alignMiddle		: 'Do �rodka',
		alignRight		: 'Do prawej',
		alignTextTop	: 'Do g�ry tekstu',
		alignTop		: 'Do g�ry',
		quality			: 'Jako��',
		qualityBest		: 'Najlepsza',
		qualityHigh		: 'Wysoka',
		qualityAutoHigh	: 'Auto wysoka',
		qualityMedium	: '�rednia',
		qualityAutoLow	: 'Auto niska',
		qualityLow		: 'Niska',
		windowModeWindow: 'Okno',
		windowModeOpaque: 'Nieprze�roczyste',
		windowModeTransparent : 'Prze�roczyste',
		windowMode		: 'Tryb okna',
		flashvars		: 'Zmienne dla Flasha',
		bgcolor			: 'Kolor t�a',
		width			: 'Szeroko��',
		height			: 'Wysoko��',
		hSpace			: 'Odst�p poziomy',
		vSpace			: 'Odst�p pionowy',
		validateSrc		: 'Podaj adres URL',
		validateWidth	: 'Szeroko�� musi by� liczb�.',
		validateHeight	: 'Wysoko�� musi by� liczb�.',
		validateHSpace	: 'Odst�p poziomy musi by� liczb�.',
		validateVSpace	: 'Odst�p pionowy musi by� liczb�.'
		// wstawka Kameleona
    ,url : 'Adres URL',
    preview : 'Podgl�d'
    // koniec wstawka Kameleona
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Sprawd� pisowni�',
		title			: 'Sprawd� pisowni�',
		notAvailable	: 'Przepraszamy, ale us�uga jest obecnie niedost�pna.',
		errorLoading	: 'B��d wczytywania hosta aplikacji us�ugi: %s.',
		notInDic		: 'S�owa nie ma w s�owniku',
		changeTo		: 'Zmie� na',
		btnIgnore		: 'Ignoruj',
		btnIgnoreAll	: 'Ignoruj wszystkie',
		btnReplace		: 'Zmie�',
		btnReplaceAll	: 'Zmie� wszystkie',
		btnUndo			: 'Cofnij',
		noSuggestions	: '- Brak sugestii -',
		progress		: 'Trwa sprawdzanie...',
		noMispell		: 'Sprawdzanie zako�czone: nie znaleziono b��d�w',
		noChanges		: 'Sprawdzanie zako�czone: nie zmieniono �adnego s�owa',
		oneChange		: 'Sprawdzanie zako�czone: zmieniono jedno s�owo',
		manyChanges		: 'Sprawdzanie zako�czone: zmieniono %l s��w',
		ieSpellDownload	: 'S�ownik nie jest zainstalowany. Chcesz go �ci�gn��?'
	},

	smiley :
	{
		toolbar	: 'Emotikona',
		title	: 'Wstaw emotikon�',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : 'element %1'
	},

	numberedlist	: 'Lista numerowana',
	bulletedlist	: 'Lista wypunktowana',
	indent			: 'Zwi�ksz wci�cie',
	outdent			: 'Zmniejsz wci�cie',

	justify :
	{
		left	: 'Wyr�wnaj do lewej',
		center	: 'Wyr�wnaj do �rodka',
		right	: 'Wyr�wnaj do prawej',
		block	: 'Wyr�wnaj do lewej i prawej'
	},

	blockquote : 'Cytat',

	clipboard :
	{
		title		: 'Wklej',
		cutError	: 'Ustawienia bezpiecze�stwa Twojej przegl�darki nie pozwalaj� na automatyczne wycinanie tekstu. U�yj skr�tu klawiszowego Ctrl/Cmd+X.',
		copyError	: 'Ustawienia bezpiecze�stwa Twojej przegl�darki nie pozwalaj� na automatyczne kopiowanie tekstu. U�yj skr�tu klawiszowego Ctrl/Cmd+C.',
		pasteMsg	: 'Prosz� wklei� w poni�szym polu u�ywaj�c klawiaturowego skr�tu (<STRONG>Ctrl/Cmd+V</STRONG>) i klikn�� <STRONG>OK</STRONG>.',
		securityMsg	: 'Zabezpieczenia przegl�darki uniemo�liwiaj� wklejenie danych bezpo�rednio do edytora. Prosz� dane wklei� ponownie w tym okienku.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'Tekst, kt�ry chcesz wklei�, prawdopodobnie pochodzi z programu Word. Czy chcesz go wyczy�cic przed wklejeniem?',
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
		title			: 'Szablony zawarto�ci',
		options : 'Template Options', // MISSING
		insertOption	: 'Zast�p aktualn� zawarto��',
		selectPromptMsg	: 'Wybierz szablon do otwarcia w edytorze<br>(obecna zawarto�� okna edytora zostanie utracona):',
		emptyListMsg	: '(Brak zdefiniowanych szablon�w)'
	},

	showBlocks : 'Poka� bloki',

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
		tag_h1		: 'Nag��wek 1',
		tag_h2		: 'Nag��wek 2',
		tag_h3		: 'Nag��wek 3',
		tag_h4		: 'Nag��wek 4',
		tag_h5		: 'Nag��wek 5',
		tag_h6		: 'Nag��wek 6',
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
		bgColorTitle	: 'Kolor t�a',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automatycznie',
		more			: 'Wi�cej kolor�w...'
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
		title			: 'Sprawd� pisowni� podczas pisania (SCAYT)',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'W��cz SCAYT',
		disable			: 'Wy��cz SCAYT',
		about			: 'Na temat SCAYT',
		toggle			: 'Prze��cz SCAYT',
		options			: 'Opcje',
		langs			: 'J�zyki',
		moreSuggestions	: 'Wi�cej sugestii',
		ignore			: 'Ignoruj',
		ignoreAll		: 'Ignoruj wszystkie',
		addWord			: 'Dodaj s�owo',
		emptyDic		: 'Nazwa s�ownika nie mo�e by� pusta.',

		optionsTab		: 'Opcje',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'J�zyki',

		dictionariesTab	: 'S�owniki',
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
		moreInfo	: 'Informacje na temat licencji mo�na znale�� na naszej stronie:',
		copy		: 'Copyright &copy; $1. Wszelkie prawa zastrze�one.'
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

	resize : 'Przeci�gnij, aby zmieni� rozmiar',

	colordialog :
	{
		title		: 'Wybierz kolor',
		options	:	'Color Options', // MISSING
		highlight	: 'Zaznacz',
		selected	: 'Wybrany',
		clear		: 'Wyczy��'
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
	
	// wstawka Kameleona
	,maska :
	{
		toolbar		: 'Wstaw mask�',
		menu		: 'Wstaw mask�',
		properties : 'W�a�ciwo�ci maski',
		title		: 'Wstaw mask�',
		name		: 'Identyfikator modu�u',
		errorName	: 'Wpisz identyfikator modu�u'
	}
	// koniec wstawka Kameleona
};
