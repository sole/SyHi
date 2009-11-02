=== Plugin Name ===
Contributors: sole
Tags: code, highlight, syntax, preformatting, whitespace, preserve
Requires at least: 2.8.5
Tested up to: 2.8.5
Stable tag: trunk

Minimalistic Syntax Highlighter plug-in which also makes sure code can still be copied and pasted into your favourite compiler!

== Description ==

This plug-in is totally designed for developers.

You know, that sort of people who post code snippets in their blog regularly and hardly use the Visual Editor,
because they write the HTML themselves. But they don't like WordPress messing with their quotes and dashes,
so this plug-in makes sure no one touches one of their lovingly crafted lines of code.

It's also very minimalistic - less than 10k, not counting the GeSHi library which is also [partially] included.

In addition, it's also lazy. It will try to avoid doing as much work as possible.
That is good, because it will not eat resources like other plug-ins out there.

== Installation ==

- Upload the full `syhi` folder to the `/wp-content/plugins/` directory
- Activate the plugin through the 'Plugins' menu in WordPress
- That's it!

**Warning:** php5 and WP2.8.5+ are required. The plug-in has not been tested with any other configuration.

== Usage ==

Whenever you want to post some syntax highlighted code, surround it with <code lang="language"></code>. That's it.

Example:

<code lang="php">
class HelloWorld()
{
	public function __construct()
	{
		echo "This is an unnecessarily long version of Hello World";
	}
}
</code>

The 'lang' attribute can be entirely omitted. You will get whitespace, quotes and dashes preservation... but no highlighting.

== Frequently Asked Questions ==

= Is there any configuration panel or settings for the plug-in? =

Not right now, but it's on the to-do list. However, don't expect to be able to modify tons of settings. This is all about minimalism!

= Can I use the Visual Editor to enter code? =

My tests prove otherwise. You'll need to use the HTML view. At least by now.

= Features that other plug-ins have but are entirely unsupported/missing for the time being =

* Ability to use [code][/code] tags instead of <code></code> tags (would solve problems with the Visual Editor and the code tags)
* Ability to show line numbers
* Ability to have nested <code> blocks

= Planned features =

 Add options for...
 * ... configuring if you want to use my css sheet, your css sheet, both, or no one at all
 * ... configuring if you want to use GeSHi, or are happy with just plain preformatted text
 * ... configuring if you want to allow commenters to post syntax highlighted code snippets

== Screenshots ==

1. Sample of code highlighted with SyHi. See how the other text is unaffected by the quote-preserving feature, and hence is fully texturized (dashes, curly quotes, you name it!)
2. Sample of code with and without highlighting, using the default kubrick theme. Notice how the first snippet is totally ruined, because it doesn't use the code tag. The second one is beatifully colored and all problematic characters are preserved. Notice also that the snippet gets a scrollbar if the lines are too wide for the column width.

== Changelog ==

= 0.0.2 =
* Added the mandatory readme.txt that I forgot in the first release, plus a couple of screenshots.

= 0.0.1 =
* First public release

== How to add extra language sets ==

Language rules are in libs/geshi/. I haven't uploaded all of the rules that come with the default distribution. If you need rules for a language which is not available, please download a full Geshi stable distribution and upload to your host the sets you require.

== Uninstall ==

Just deactivate the plug-in, and remove its folder from the wp-content/plugins directory.

== Thanks ==

Big thanks to the creators of the GeSHi library. Without it, the output of this plug-in would be very boring.
Here's its homepage: http://qbnz.com/highlighter/

The following plug-ins were invaluable sources of inspiration and how-to-do-it-right:

* Preserve Code Formatting, by  Scott Reilly (http://coffee2code.com)
* Code Snippet, by Roman Roan. Now maintained by Wyatt Neal (http://blog.hackerforhire.org/code-snippet/)

You could say this plug-in is the sum of the essence of both plug-ins.