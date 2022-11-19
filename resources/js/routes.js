export default [
    { path: '/', redirect: '/dashboard' },

    {
        path: '/dashboard',
        name: 'dashboard',
        component: require('./screens/dashboard').default,
    },

    {
        path: '/running/:flowId',
        name: 'running-flows-preview',
        component: require('./screens/flows/flow').default,
    },

    {
        path: '/completed/:flowId',
        name: 'completed-flows-preview',
        component: require('./screens/flows/flow').default,
    },

    {
        path: '/failed/:flowId',
        name: 'failed-flows-preview',
        component: require('./screens/flows/flow').default,
    },

    {
        path: '/:type',
        name: 'flows',
        component: require('./screens/flows/index').default,
    },
];
