import { usePage } from '@inertiajs/vue3';

let userPermissions = [];

const loadUserPermissions = () => {
  if (userPermissions.length === 0) {
    const page = usePage();
    userPermissions = [...page.props.auth.user.permissions];
  }
};

export const can = (permission) => {
  loadUserPermissions();
  return userPermissions.includes(permission);
};

export const canShowRightMenu = () => {
  const permissions = [
    'locations.index',
    'contacttypes.index',
    'professionaltypes.index',
    'customertypes.index',
    'categories.index',
    'urgencytypes.index',
  ];
  return permissions.some(permission => can(permission));
};
