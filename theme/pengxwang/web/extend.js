(function() {
  'use strict';
  let setup = {};
  setup.adaptivity = {
    indexDrawer({ indexElement, layoutElement }) {
      const closedClass = 'js-drawer-closed';
      layoutElement.addEventListener('click', (event) => {
        if (event.target !== indexElement && event.target !== layoutElement) { return; }
        indexElement.classList.toggle(closedClass);
      });
      indexElement.classList.add(closedClass);
    },
    indexExpansion({ indexElement, postElement }) {
      const expandedClass = 'js-expanded';
      const visibleClass = 'js-expanded-visible';
      const expandDelay = 1000 * parseFloat(getComputedStyle(indexElement).getPropertyValue('--expand-delay'));
      const expandDuration = 1000 * parseFloat(getComputedStyle(indexElement).getPropertyValue('--expand-duration'));
      let logoElement = indexElement.querySelector('.logo');
      let logoAnchorElement = logoElement.querySelector('a');
      logoElement.addEventListener('click', (event) => {
        if (event.target !== logoElement) { return; }
        postElement.scrollTop = 0;
      });
      logoAnchorElement.addEventListener('click', (event) => {
        if (indexElement.classList.contains(expandedClass)) {
          indexElement.classList.remove(visibleClass);
          setTimeout(() => { indexElement.classList.remove(expandedClass); }, expandDuration + expandDelay);
        } else {
          indexElement.classList.add(expandedClass);
          setTimeout(() => { indexElement.classList.add(visibleClass); }, 0);
        }
        event.preventDefault();
      });
    },
  };
  setup.extension = {
    accordion({ navElement }) {
      const { Accordion } = HLF;
      Accordion.extend(navElement);
    },
    slideshow({ slideshowElement }) {
      if (slideshowElement === null) { return; }
      const { SlideShow } = HLF;
      let slideshow = SlideShow.extend(slideshowElement);
      let counterElement = slideshowElement.querySelector('.counter');
      slideshowElement.addEventListener('hlfssslidechange', (event) => {
        let { element, index } = event.detail;
        counterElement.textContent = `${index + 1}`;
      });
    },
    tips({ indexElement, navElement, footerElement, postElement, articleElement }) {
      const { Tip } = HLF;
      let navTip = Tip.extend(navElement.querySelectorAll('[title]'), {
        snapTo: 'y', contextElement: navElement, viewportElement: indexElement
      });
      let footerTip = Tip.extend(footerElement.querySelectorAll('[title]'), {
        snapTo: 'x', contextElement: footerElement, viewportElement: indexElement
      });
      Array.from(articleElement.children).forEach((sectionElement) => {
        if (sectionElement.classList.contains('external')) {
          let externalTip = Tip.extend(sectionElement.querySelectorAll('[title]'), {
            snapTo: 'y', contextElement: sectionElement, viewportElement: postElement
          });
          return;
        }
        let tip = Tip.extend(sectionElement.querySelectorAll('[title]'), {
          snapTo: 'x', contextElement: sectionElement, viewportElement: postElement
        });
      });
    },
  };
  document.addEventListener('DOMContentLoaded', () => {
    let indexElement = document.querySelector('#index');
    let layoutElement = indexElement.parentElement;
    let navElement = indexElement.querySelector('nav');
    let footerElement = indexElement.querySelector('footer');

    let postElement = document.querySelector('#post');
    let articleElement = postElement.querySelector('article');
    let slideshowElement = postElement.querySelector('.slideshow');

    if (document.body.clientWidth < parseFloat(getComputedStyle(layoutElement).maxWidth)) {
      if (getComputedStyle(navElement).display === 'none') {
        setup.adaptivity.indexExpansion({ indexElement, postElement });
      } else {
        setup.adaptivity.indexDrawer({ indexElement, layoutElement });
      }
    }
    setup.extension.accordion({ navElement });
    setup.extension.slideshow({ slideshowElement });
    setup.extension.tips({ indexElement, navElement, footerElement, postElement, articleElement });

    let printElement = postElement.querySelector('button.print');
    if (printElement !== null) {
      printElement.addEventListener('click', () => print());
    }
  });
}());
