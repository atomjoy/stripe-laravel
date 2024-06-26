{
	// GTK Remove Bar (set manualy from settings)
	// "window.titleBarStyle": "custom",

	// GTK Disable open folder file popup
	// "files.simpleDialog.enable": true,

	// Disable git
	"git.enabled": false,

	// Php
	"php.validate.executablePath": "C:/xampp/php/php.exe",
	// "php.validate.executablePath": "/bin/php",

	// Window zoom/scale
	"window.zoomLevel": 0,

	// Workbench style
	"workbench.tree.indent": 20,
	"workbench.tree.renderIndentGuides": "always",

	// Icons
	"workbench.iconTheme": "material-icon-theme",

	// File
	"files.trimTrailingWhitespace": true,

	// Style
	"editor.fontSize": 16,
	"editor.fontFamily": "'JetBrains Mono','Fira Code',Consolas,monospace",
	"editor.insertSpaces": true, // Use spaces no tabs
	"editor.detectIndentation": false,
	"editor.fontLigatures": true, // Enables font ligatures
	"editor.wordWrap": "off",
	"editor.lineHeight": 0, // Use 0 to compute the lineHeight from the fontSize.
	"editor.letterSpacing": 0.5,
	"editor.tabSize": 4,

	// Formatter
	"editor.formatOnSave": true,
	"editor.defaultFormatter": "esbenp.prettier-vscode",

	// Prettier settings @ext:esbenp.prettier-vscode
	"prettier.useEditorConfig": false,
	"prettier.singleQuote": true,
	"prettier.semi": false,
	"prettier.jsxSingleQuote": true,
	"prettier.useTabs": true, // Change spaces to tabs
	"prettier.bracketSameLine": true, // Dont break > tag end

	// Line length
	"editor.wordWrapColumn": 300, // Dont break line
	"html.format.wrapLineLength": 300, // Dont break line
	"prettier.printWidth": 300, // Dont break line

	// Laravel Blade Snippets formatter @ext:onecentlin.laravel-blade
	"blade.format.enable": true,

	// Format files
	"[markdown]": {
		"editor.unicodeHighlight.ambiguousCharacters": false,
		"editor.unicodeHighlight.invisibleCharacters": false,
		"editor.wordWrap": "off",
		"editor.formatOnSave": true,
		"editor.formatOnPaste": true,
		"editor.defaultFormatter": "DavidAnson.vscode-markdownlint"
	},
	"[javascript]": {
		"editor.formatOnSave": true,
		"editor.defaultFormatter": "esbenp.prettier-vscode"
	},
	"[vue]": {
		"editor.formatOnSave": true,
		"editor.defaultFormatter": "esbenp.prettier-vscode"
	},
	"[css]": {
		"editor.formatOnSave": true,
		"editor.defaultFormatter": "esbenp.prettier-vscode"
	},
	"[ts]": {
		"editor.formatOnSave": true,
		"editor.defaultFormatter": "esbenp.prettier-vscode"
	},
	"[php]": {
		"editor.tabSize": 4,
		"editor.insertSpaces": false, // Change spaces to tabs
		"editor.formatOnSave": true,
		"editor.defaultFormatter": "bmewburn.vscode-intelephense-client",
		"editor.wordWrapColumn": 1000
	},
	"[blade]": {
		"editor.tabSize": 4,
		"editor.insertSpaces": false, // Change spaces to tabs
		"editor.formatOnSave": true,
		"editor.defaultFormatter": "onecentlin.laravel-blade",
		"editor.wordWrapColumn": 1000,
		"editor.autoClosingBrackets": "always"
	},
	"[xml]": {
		"editor.defaultFormatter": "DotJoshJohnson.xml"
	},

	"editor.codeActionsOnSave": {
		"source.fixAll.markdownlint": "explicit"
	},

	// Laravel facades error
	"intelephense.diagnostics.undefinedTypes": false,
	"intelephense.diagnostics.undefinedMethods": false,
	"intelephense.environment.phpVersion": "8.1.6",
	"workbench.startupEditor": "none",
	"explorer.confirmDragAndDrop": false,
	"workbench.colorTheme": "Tokyo Night Pro",

	"files.associations": {
		"*.env": "properties",
		"*.env.*": "properties"
	}

	// Corrupt SynthWawe 80 Theme
	// "synthwave84.disableGlow": false,
	// "synthwave84.brightness": 0.45
}
