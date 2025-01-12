export default [
  {
    title: 'Home',
    to: { name: 'root' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'Second page',
    to: { name: 'second-page' },
    icon: { icon: 'tabler-file' },
  },
  {
    title: 'Gestion catégorie',
    to: { name: 'categories' },
    icon: { icon: 'tabler-category' },
  },
  {
    title: 'Gestion des etablissmenets',
    to: { name: 'school' },
    icon: { icon: 'tabler-school' },
  },
  {
    title: 'Gestion des Admins',
    to: { name: 'admins' },
    icon: { icon: 'tabler-users' },
  },
  {
    title: 'Gestion des Formations',
    icon: { icon: 'tabler-users' },
    children:[{
      title: 'Gestion Type de formation',
      to: { name: 'formations-type' },
      // icon: { icon: 'tabler-users' },    
    }],
    to: { name: 'formations' },
  },
  // {
  //   title: 'Gestion Type de formation',
  //   to: { name: 'formations-type' },
  //   icon: { icon: 'tabler-users' },
  // },
  {
    title: 'Gestion du Personnel',
    to: { name: 'employes' },
    icon: { icon: 'tabler-users' },
  },
]
