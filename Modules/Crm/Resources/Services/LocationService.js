import axios from 'axios';
import datatable from '@Core/Services/Datatable';

const createNodes = (originalNodes) => {
  const map = {};
  const roots = [];

  // First pass: create a map of all nodes and initialize them
  originalNodes.forEach((item, index) => {
    map[item.id] = {
      key: String(index), // Temporary key, will be updated later
      label: item.name,
      data: item, // Or the whole item if needed: item
      icon: 'pi pi-fw pi-map-marker', // Default icon
      children: [],
    };
  });

  // Second pass: link children to parents
  originalNodes.forEach((item) => {
    const node = map[item.id];
    if (item.parent_id && map[item.parent_id]) {
      map[item.parent_id].children.push(node);
    } else {
      roots.push(node);
    }
  });

  // Helper to generate hierarchical keys recursively
  const generateKeys = (nodes, parentKey = '') => {
    nodes.forEach((node, index) => {
      const currentKey = parentKey ? `${parentKey}-${index}` : String(index);
      node.key = currentKey;

      // Customize icon based on type or level if needed
      if (node.children.length > 0) {
        node.icon = 'pi pi-fw pi-folder'; // Icon for nodes with children
      } else {
        node.icon = 'pi pi-fw pi-map-marker'; // Icon for leaf nodes
      }

      if (node.children.length > 0) {
        generateKeys(node.children, currentKey);
      }
    });
  };

  generateKeys(roots);

  return roots;
};

const getDataNode = (nodes, key) => {
  const keys = Object.keys(key);
  if (keys.length === 0) {
    return null;
  }

  const parts = keys[0].split('-').map(Number);
  let currentNodes = nodes;
  let element;

  for (const part of parts) {
    element = currentNodes[part].data;
    currentNodes = currentNodes[part].children ?? currentNodes[part];
  }
  return element;
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
