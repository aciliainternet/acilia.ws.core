import { showError as showErrorNotification } from '../../modules/a_notifications';

function getStylesByType(type) {
  return {
    wrapperSpecificClass: `c-alert--border-${type}`,
    icon: `<i class="fal fa-exclamation-triangle u-color-${type}"></i>`,
  };
}

function hide(messageWrapperIdentifier) {
  try {
    document.querySelectorAll(messageWrapperIdentifier).forEach((message) => {
      message.classList.add('u-hidden');
    });
  } catch (error) {
    console.error(error);
  }
}

/**
 * Show the cropper message
 * @param string messageWrapperIdentifier - The DOM identifier of the wrapper.
 * @param string messageText - The message to show.
 * @param string type - The type of message, ex: success, warning, danger, info.
 * @return void
 */
function show(messageWrapperIdentifier, messageText, type) {
  try {
    const wrapper = document.querySelector(messageWrapperIdentifier);

    if (wrapper) {
      const specificStyles = getStylesByType(type);
      wrapper.classList.add(specificStyles.wrapperSpecificClass);
      wrapper.innerHTML = `${specificStyles.icon}${messageText}`;
      wrapper.classList.remove('u-hidden');

      // After a time the message will hide
      setTimeout(() => {
        wrapper.classList.add('u-hidden');
      }, 5000);
    } else {
      // In case there isn't html for the message, we show the default notification with the error
      showErrorNotification(messageText);
    }
  } catch (error) {
    console.error(error);
  }
}

export {
  hide,
  show,
};
