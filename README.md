# ESHOP API

GraphQL API for Roiwell based projects.

## Functions
- Built for work with Liquid Design ecosystem
- Auto types creation from PHP classes to TypeRegister (Storm entities)
- Autoloading of Queries and Mutations from Nette Container
- Caching of schema
- Universal CRUD resolvers for classes
- Recursive data fetcher of Storm entities from database based on requested query (highly optimized - makes only one query per entity class - simulating dataloader)
- 