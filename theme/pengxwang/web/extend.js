(function() {
  'use strict';

  class Accordion {
    static get debug() {
      return false;
    }
    static get defaults() {
      return {
        cursorItemClass: 'active',
        // featureCount: 1,
        itemsSelector: 'li:not(:first-child)',
        sectionSelector: 'ul',
        triggerSelector: 'button.accordion',
      };
    }
    static toPrefix(context) {
      switch (context) {
        case 'event': return 'hlfac';
        case 'data': return 'hlf-ac';
        case 'class': return 'ac';
        case 'var': return 'ac';
        default: return 'hlf-ac';
      }
    }
    init() {
      this._sections = [];
      Array.from(this.element.querySelectorAll(this.sectionSelector))
        .forEach(this._setUpSection);
    }
    _setUpSection(sectionElement) {
      let itemElements = Array.from(sectionElement.querySelectorAll(this.itemsSelector));
      let section = {
        isFolded: false,
        itemElements,
        sectionElement,
        triggerElement: sectionElement.querySelector(this.triggerSelector),
      };
      this._sections.push(section);
      let on = itemElements.some(el => el.classList.contains(this.cursorItemClass));
      this._toggleSection(section, on);
    }
    _toggleSection(section, on) {
      let { itemElements, sectionElement } = section;
      sectionElement.classList.toggle(this.className('folded'), !on);
      section.isFolded = !on;
    }
  }

  HLF.buildExtension(Accordion, {
    autoBind: true,
    // autoListen: true,
    compactOptions: true,
    // mixinNames: ['css', 'selection'],
  });

  document.addEventListener('DOMContentLoaded', () => {
    let navElement = document.querySelector('#index > nav');
    let accordion = Accordion.extend(navElement);
  });

}());
