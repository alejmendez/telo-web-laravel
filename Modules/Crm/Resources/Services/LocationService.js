import axios from 'axios';
import datatable from '@Core/Services/Datatable';

const createNodes = (originalNodes) => {
  const map = {};
  const roots = [];

  originalNodes.forEach((item) => {
    map[item.id] = {
      key: String(item.id),
      label: item.name,
      data: item,
      icon: 'pi pi-fw pi-map-marker',
      children: [],
    };
  });

  originalNodes.forEach((item) => {
    const node = map[item.id];
    if (item.parent_id && map[item.parent_id]) {
      map[item.parent_id].children.push(node);
    } else {
      roots.push(node);
    }
  });

  const setIcons = (nodes) => {
    nodes.forEach((node) => {
      if (node.children.length > 0) {
        node.icon = 'pi pi-fw pi-folder';
        setIcons(node.children);
      } else {
        node.icon = 'pi pi-fw pi-map-marker';
      }
    });
  };

  setIcons(roots);

  return roots;
};

const getDataNode = (nodes, key) => {
  const keys = Object.keys(key);
  if (keys.length === 0) {
    return null;
  }

  const targetKey = keys[0];
  let found = null;

  const search = (currentNodes) => {
    for (const node of currentNodes) {
      if (String(node.key) === targetKey) {
        found = node.data;
        return true;
      }

      if (node.children && node.children.length > 0 && search(node.children)) {
        return true;
      }
    }

    return false;
  };

  search(nodes);

  return found;
};

const list = async (lazyParams) => {
  const response = await datatable.list(route('locations.index'), lazyParams);

  return response.data;
};

const del = async (id) => {
  await axios.delete(route('locations.destroy', { id }));
  return true;
};

export default {
  createNodes,
  getDataNode,
  list,
  del,
};
