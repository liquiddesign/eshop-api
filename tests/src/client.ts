import {ApolloClient, InMemoryCache} from '@apollo/client/core';

const client = new ApolloClient({
    uri: 'http://18.196.72.110/eshop-api/',
    cache: new InMemoryCache(),
});

export default client;