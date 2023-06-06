import { GraphQLClient } from "graphql-request";
// this https rejectUnauthorized changes only needed if the domain name is self signed and you want to use https inside docker network. 
//If the domain is public and has dns challanged singed certification this is not needed.
//import https from 'https';
//export const httpsAgent = new https.Agent({ rejectUnauthorized: false });
//export const gqlClient = new GraphQLClient("https://cms.example.test/wp/graphql", { agent: httpsAgent });
export const gqlClient = new GraphQLClient("http://cms.example.test:8080/wp/graphql");