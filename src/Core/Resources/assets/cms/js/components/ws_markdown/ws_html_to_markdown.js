import TurndownService from "turndown";

const turndownService = new TurndownService();

turndownService.addRule("italic", {
  filter: function (node) {
    return (
      node.nodeName === "I" ||
      node.nodeName === "EM" ||
      (node.nodeName === "SPAN" &&
        node.style &&
        node.style.fontStyle === "italic")
    );
  },
  replacement: function (content) {
    return "*" + content + "*";
  },
});

turndownService.addRule("boldSpan", {
  filter: function (node) {
    return node.nodeName === "SPAN" && node.style.fontWeight === "700";
  },
  replacement: function (content) {
    return "**" + content + "**";
  },
});

turndownService.addRule("h1", {
  filter: ["h1"],
  replacement: function (content) {
    return "# " + content + "\n\n";
  },
});

turndownService.addRule("h2", {
  filter: ["h2"],
  replacement: function (content) {
    return "## " + content + "\n\n";
  },
});

export function convertHtmlToMarkdown(html) {
  return turndownService.turndown(html);
}
