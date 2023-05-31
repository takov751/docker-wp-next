import { gql } from "graphql-request";
import { gqlClient } from "@/lib/gql";

export default async function Home() {
  const document = gql`
    query GetPostsPageId {
      readingSettings {
        pageForPosts
      }
    }
  `;

  const result = await gqlClient.request(document);

  return (
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <pre>{JSON.stringify(result, null, 2)}</pre>
    </main>
  );
}
