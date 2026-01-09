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
      link: route('countries.index'),
      text: 'menu.right.countries',
      icon: 'globe',
      active: currentComponent.startsWith('countries'),
      can: can('countries.index'),
    },
    {
      link: route('cities.index'),
      text: 'menu.right.cities',
      icon: 'location_city',
      active: currentComponent.startsWith('cities'),
      can: can('cities.index'),
    },
  ];

  const menuItemsFiltered = menuItems.filter((item) => item.can);

  return menuItemsFiltered;
};
