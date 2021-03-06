ThrdPlace-Search-Engine-And-Recommend-System
============================================

This is a project implemented in the Software Engineering course at University of Southern California. Our client is [thrdPlace.com](http://thrdplace.com/)

##Background of ThrdPlace Social Networking
* goal: aggregates local activity to create global movements based on shared interest
* business model: SaaS model, clients pay a monthly subscription to thrdPlace to promote their community outreach and development

##Background of the project
* provide search functions (with filters and sorting) for users
* provide recommendtation to users according to their profile
* Build on Apache Solr
  * Provide advanced search among several Solr collections with sorting and faceted search.
  * Identify recommendation abstracts by querying the Carrot2 clustering engine on Solr.

##Apache Solr
 * enterprise-level search engine
 * full-text search, hit highlight, faceted search, dynamic clustering, database integration, and support rich document(PDF, word) 
 * Written in **Java**, runs as a standalone server within a servlet container such as Tomcat or Jetty
 * Uses **Lucene** as core library
 * Provide REST-like HTTP/XML and JSON APIs
