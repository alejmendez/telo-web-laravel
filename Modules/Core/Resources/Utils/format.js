export const formatNumber = (number, decimals = 2) => {
  return new Intl.NumberFormat('es-CL', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals,
  }).format(number);
};

export const idFormater = (id, prefix = '', length = 5) => {
  return prefix + id.toString().padStart(length, '0');
};
