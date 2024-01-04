import { convertHtmlToMarkdown } from "./ws_html_to_markdown";

export function pasteHTMLAsMarkdown(event) {
  event.preventDefault();
  const clipboardData = event.clipboardData || window.clipboardData;
  const pastedData = clipboardData.getData("text/html");
  const tempHtml = document.createElement("div");
  tempHtml.innerHTML = pastedData;
  const bTags = tempHtml.querySelectorAll("b[id]");
  bTags.forEach((bTag) => {
    while (bTag.firstChild) {
      bTag.parentNode.insertBefore(bTag.firstChild, bTag);
    }
    bTag.parentNode.removeChild(bTag);
  });
  return convertHtmlToMarkdown(tempHtml.innerHTML);
}
