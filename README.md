# ESHOP API

GraphQL API for Roiwell based projects.

## Functions
- Built for work with Liquid Design ecosystem
- Auto types creation from PHP classes to TypeRegister (Storm entities)
- Autoloading of Queries and Mutations from Nette Container
- Universal CRUD resolvers for classes
- Recursive data fetcher of Storm entities from database based on requested query (highly optimized - makes only one query per entity class)

## TODO
- Create/Update operations works, but only in 1 level -> write recursive function => Several problems
  - What to do when updating relation? in graphql you can pass whole nested object
    - If id is specified, find object -> update it and connect to parent
    - If id is specified and not found -> error
    - If no id -> create object and connect to parent
  - This is hard to document, general create and update mutations uses different inputs, one with required fields to create, other all optional
    - In this situation, you can specify only one type with all optional fields
- Make typesafe selecting (check with schema)
- It is not affecting performance very much, maybe not needed

## BACKLOG
- Distribute types and resolvers to appropriate packages, make this Nette extension
- Authentication/Authorization - you can do it manually in resolve fn, maybe automatic function on resolver type?
- Try https://github.com/joonlabs/php-graphql/, better benchmarks, very similar usage like webonyx