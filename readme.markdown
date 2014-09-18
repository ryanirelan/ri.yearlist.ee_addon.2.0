# YearList 2.1
## Get a four digit year for your entries.

The Year Listing plugin is a simple way to get a distinct 4 digit year for your entries. This way you can list out years for archives.




	{exp:yearlist channel="yourchannel" category="1" site="1"}

		{years}

	{/exp:yearlist}

That will return an array of years. Use {years} (plural, to avoid a variable conflict) to print them to the screen and wrap in any markup needed. There are currently no line breaks or HTML associated with this plugin.

The category parameter is optional, and if you leave it out, the plugin will search across all categories. The site parameter is optional, and if you leave it out, the plugin will search the default site.

This plugin requires ExpressionEngine 2.0.