# TinyUrl

Module that generates slugs for Url nodes and also leverages
GraphQL for querying, adding, and removing url nodes.

## Requirements

Requires GraphQL module and Drupal core node.

On Url node, url field is required. Slug is optional, but will be generated if
left empty.

## Usage

Provides route/alias to allow for short urls, which will redirect to the final
destination/url configured on the url node.

See below:

`/s/<slug>`

Swap out <slug> with the entered or generated slug. Redirection will take place
for internal and external urls.

## GraphQL Examples

See below for examples.

### Query Url node

Swap out $id with the node id of the url.

It will return the id, slug, and url associated with slug.

```graphql
{
  url(id: $id) {
    id
    slug
    url
  }
}
```

### Create Url Node

Create Url node with just an url. Slug should be generated on this call.

```graphql
mutation {
  createUrl(data: { url: "https://google.ca" }) {
    url {
      id
      slug
      url
    }
    errors
  }
}
```

Create Url node with url and slug provided.

```graphql
mutation {
  createUrl(data: { url: "https://google.ca", slug: "google-ca" }) {
    url {
      id
      slug
      url
    }
    errors
  }
}
```

### Delete Url Node

Delete Url node with url node id.

Swap out $id with the node id of the url.

```graphql
mutation {
  deleteUrl(id: $id) {
    url {
      id
      slug
      url
    }
    errors
  }
}
```
