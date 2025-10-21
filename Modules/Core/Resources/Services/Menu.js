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
    // {
    //   link: route('backup.index'),
    //   text: 'menu.right.backup',
    //   icon: 'backup',
    //   active: currentComponent.startsWith('Backup'),
    //   can: can('backup.index'),
    // },
  ];

  const menuItemsFiltered = menuItems.filter((item) => item.can);

  return menuItemsFiltered;
};
