(function() {
  'use strict';

  class Accordion {
    static get debug() {
      return false;
    }
    static get defaults() {
      return {
        autoCollapse: true,
        cursorItemClass: 'active',
        featureCount: 1,
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
    deinit() {
      this._sections.forEach(this._tearDownSection);
      this._sections = [];
    }
    _onTriggerClick(event) {
      let section = this._sections.find(section => section.triggerElement === event.currentTarget);
      this._toggleSectionFolding(section);
    }
    _setUpSection(sectionElement) {
      let itemElements = Array.from(sectionElement.querySelectorAll(this.itemsSelector));
      let section = {
        hasCursor: itemElements.some(el => el.classList.contains(this.cursorItemClass)),
        isFolded: false,
        itemElements,
        sectionElement,
        triggerElement: sectionElement.querySelector(this.triggerSelector),
      };
      this._sections.push(section);
      this._toggleSectionFolding(section, !section.hasCursor);
      this._toggleSectionEventListeners(true, section);
    }
    _tearDownSection(section) {
      this._toggleSectionEventListeners(false, section);
    }
    _toggleSectionFolding(section, folded) {
      const { hasCursor, isFolded } = section;
      if (hasCursor && folded) { return; }
      if (folded == null) { folded = !isFolded; }
      else if (isFolded === folded) { return; }
      if (this.autoCollapse && !folded) {
        this._sections.filter(s => s !== section)
          .forEach(s => this._toggleSectionFolding(s, true));
      }
      let { itemElements, sectionElement } = section;
      itemElements.slice(this.featureCount)
        .forEach(el => el.style.display = folded ? 'none' : 'block');
      sectionElement.classList.toggle(this.className('folded'), folded);
      section.isFolded = folded;
    }
    _toggleSectionEventListeners(on, section) {
      let { triggerElement } = section;
      this.toggleEventListeners(on, {
        'click': this._onTriggerClick,
      }, triggerElement);
    }
  }

  HLF.buildExtension(Accordion, {
    autoBind: true,
    compactOptions: true,
    mixinNames: ['event'],
  });

  document.addEventListener('DOMContentLoaded', () => {
    let navElement = document.querySelector('#index > nav');
    let accordion = Accordion.extend(navElement);
    const { Tip } = HLF;
    let navTip = Tip.extend(navElement.querySelectorAll('[title]'), {
      snapTo: 'y', contextElement: navElement
    });
    let footerElement = document.querySelector('#index > footer');
    let footerTip = Tip.extend(footerElement.querySelectorAll('[title]'), {
      snapTo: 'x', contextElement: footerElement
    });

    Array.from(document.querySelector('#post section')).forEach((sectionElement) => {
      if (sectionElement.classList.contains('external')) {
        let externalTip = Tip.extend(sectionElement.querySelectorAll('[title]'), {
          snapTo: 'y', contextElement: sectionElement
        });
        return;
      }
      let footerTip = Tip.extend(sectionElement.querySelectorAll('[title]'), {
        snapTo: 'x', contextElement: sectionElement
      });
    });
  });

}());
