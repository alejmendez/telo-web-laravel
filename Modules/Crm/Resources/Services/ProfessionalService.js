import axios from 'axios';
import datatable from '@Core/Services/Datatable';

const list = async (lazyParams) => {
  const response = await datatable.list(route('professionals.index'), lazyParams);

  return response.data;
};

const del = async (id) => {
  await axios.delete(route('professionals.destroy', { id }));
  return true;
};

export default {
  list,
  del,
};
