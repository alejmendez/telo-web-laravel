import { toRaw } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { can } from '@Auth/Services/Auth';

export const menuElements = (currentComponent) => {
  const page = usePage();
  const menuItems = toRaw(page.props.menu);

  return menuItems;
};

export const menuElementsRight = (currentComponent) => {
  const menuItems = [
    {
      link: route('locations.index'),
      text: 'menu.right.locations',
      icon: 'globe',
      active: currentComponent.startsWith('locations'),
      can: can('locations.index'),
    },
    {
      link: route('contacttypes.index'),
      text: 'menu.right.contacttypes',
      icon: 'people',
      active: currentComponent.startsWith('contacttypes'),
      can: can('contacttypes.index'),
    },
    {
      link: route('customertypes.index'),
      text: 'menu.right.customertypes',
      icon: 'business',
      active: currentComponent.startsWith('customertypes'),
      can: can('customertypes.index'),
    },
    {
      link: route('professionaltypes.index'),
      text: 'menu.right.professionaltypes',
      icon: 'construction',
      active: currentComponent.startsWith('professionaltypes'),
      can: can('professionaltypes.index'),
    },
  ];

  const menuItemsFiltered = menuItems.filter((item) => item.can);

  return menuItemsFiltered;
};
