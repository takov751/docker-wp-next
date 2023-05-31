import { GraphQLClient } from "graphql-request";

export const gqlClient = new GraphQLClient("http://cms:9000/wp/graphql");
