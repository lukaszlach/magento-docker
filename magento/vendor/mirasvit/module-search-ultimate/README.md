# Sphinx Search Ultimate

## 1.0.5
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1

---

## 1.0.4
*(2016-05-06)*

* Search Sphinx 1.0.14
* Search Autocomplete & Suggest 1.0.19
* Search Spell Correction 1.0.5

---

## 1.0.2
*(2016-04-20)*

* Search Sphinx 1.0.13
* Search Autocomplete & Suggest 1.0.15
* Search Spell Correction 1.0.4

---

## 1.0.1
*(2016-04-14)*

* Search Sphinx 1.0.11
* Search Autocomplete & Suggest 1.0.13
* Search Spell Correction 1.0.2


------
# Search Sphinx
## 1.0.24
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1
* Fixed an issue with "Enable redirect from 404 to search results"

---


## 1.0.23
*(2016-06-14)*

#### Features
* Ability to reset sphinx daemon

---

## 1.0.22
*(2016-06-08)*

#### Fixed
* Fixed an issue with multistore results

---

## 1.0.21
*(2016-06-07)*

#### Improvements
* Added ability to search by Magefan Blog module

---

## 1.0.20
*(2016-05-24)*

#### Improvements
* Added special chars to sphinx configuration charset table

---

## 1.0.19
*(2016-05-19)*

#### Improvements
* Moved SphinxQL lib to module

#### Fixed
* Fixed an issue with synonyms

---

## 1.0.18
*(2016-05-17)*

#### Improvements
* Added additional file extension exceptions to 404 observer

#### Fixed
* Fixed an issue with min_word_len (search with dashes 0-1)

---

## 1.0.17
*(2016-05-16)*

#### Bugfixes
* SSU2-13 - Fix issue with synonyms

---

## 1.0.15, 1.0.16
*(2016-05-12)*

#### Improvements
* Improved performance of query builder

#### Fixed
* Fixed an sphinx query error after adding new attribute

---

## 1.0.14
*(2016-04-26)*

#### Fixed
* Fixed an issue with cronjobs

---

## 1.0.13
*(2016-04-20)*

#### Improvements
* Added console command for reindex search indexes

#### Fixed
* Fixed an issue with search by child product SKU
* Fixed css issue with active search tab, when HTML minification is enabled
* Fixed an issue with menu
* Fixed an issue with score builder for mysql engine

---

## 1.0.12
*(2016-04-07)*

#### Fixed
* Fixed an issue with area code (cli mode)
* Fixed an javascript error when html minification is enabled
* Fixed an issue with plural queries

---

## 1.0.11
*(2016-03-25)*

#### Improvements
* Integrated Mirasvit Knowledge Base

---

## 1.0.10
*(2016-03-17)*

#### Improvements
* Default index configuration
* Ability to search products only in active categories

#### Fixed
* Fixed possible issue with score sql query
* Fixed an issue with results limit

#### Documentation
* Description for Search only by active categories
* Updated installation steps

---

## 1.0.9
*(2016-03-09)*

#### Improvements
* Default index configuration
* Improved feature 404 to search
* Console commands for import/remove synonyms/stopwords
* Added default lemmatizer for EN, DE
* Improved sphinx configuration file
* Fallback engine for sphinx
* SSU2-9 -- Search by Mirasvit Blog MX
* i18n

#### Documentation
* Updated installation steps
* Information about synonyms and stopwords

#### Fixed
* Fixed an issue with stopwords import controller
* Added Symfony/Yaml to required packages
* Fixed an issue with importing synonyms and stopwords
* Fixed an issue with product list toolbar
* Fixed compatibility issue with Manadev_LayeredNavigation
* SSU2-8 -- mysql2 not found, when save product

---

## 1.0.8
*(2016-02-24)*

#### Fixed
* Fixed an issue with segmentation fault (PHP7) during reindex

---

## 1.0.7
*(2016-02-15)*

#### Fixed
* Fixed an issue with special chars in sphinx query (@)
* Fixed an issue with "Default Category" in search results for category search index
* Updated MCore version
* Formatting
* Fixed an issue with number of products at category pages (limit, offset)

---

## 1.0.6
*(2016-02-02)*

#### Bugfixes
* Fixed an issue with NOT cacheable block "search.google.sitelinks"
* Fixed an issue with upgrade script (synonyms and stopwords)
* SSU2-3 -- Fixed an issue with sh output in console (sh: searchd: command not found)

---

## 1.0.5
*(2016-01-31)*

#### Features
* SSU2-1 - Multi-store search results

#### Bugfixes
* Itegration tests

---

---
# Search Autocomplete & Suggest
## 1.0.28
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1

---

## 1.0.27
*(2016-06-22)*

#### Fixed
* Fixed an issue with ajax loader

---


## 1.0.26
*(2016-05-30)*

#### Fixed
* Fixed an issue with catalog layer

---

## 1.0.25
*(2016-05-26)*

#### Fixed
* Fixed an issue with duplicating Popular suggestions (letter register)

---

## 1.0.24
*(2016-05-20)*

#### Improvements
* Image selection for products autocomplete
* Ability to define ignored words for "Hot Searches"

---

## 1.0.23
*(2016-05-17)*

#### Fixed
* Fixed an issue with possible search layer exception

---

## 1.0.21, 1.0.22
*(2016-05-11)*

#### Fixed
* Fixed an issue with translations .html templates

---

## 1.0.20
*(2016-05-08)*

#### Fixed
* Fixed an issue with wrong autocomplete position on mobile devices

---

## 1.0.19
*(2016-05-06)*

#### Fixed
* Fixed an issue with wrong currency convert rate
* Fixed an issue with multi-store configuration

---

## 1.0.17
*(2016-04-29)*

#### Fixed
* Fixed an issue with tax

---

## 1.0.16
*(2016-04-27)*

#### Fixed
* Fixed an issue with html entity chars

---

## 1.0.15
*(2016-04-11)*

#### Fixed
* Fixed possible issue with http/https ajax urls
* Fixed an issue with cache warmer
* Fixed an issue with behaviour for popular search queries

---

## 1.0.14
*(2016-04-06)*

#### Improvements
* Hot Searches

---

## 1.0.13
*(2016-04-1)*

#### Improvements
* Performance and Styles

---

## 1.0.12
*(2016-03-25)*

#### Improvements
* Integrated Mirasvit Knowledge Base

---

## 1.0.11
*(2016-03-23)*

#### Improvements
* Display full category path for categories
* Default configuration for indexes

#### Fixed
* Fixed an issue with hiding placeholder before redirect to "View all results"
* Fixed an issue with selection not active indexes

---


## 1.0.10
*(2016-03-11)*

#### Improvements
* Improved loader logic

#### Fixed
* Fixed issue with FrontController headers

---

## 1.0.9
*(2016-03-09)*

#### Fixed
* Fixed an issue with price formatting
* Fixed issue with FrontController headers

#### Documentation
* Updated installation steps

---

## 1.0.8
*(2016-03-06)*

#### Improvements
* SSU2-9 -- Search by Mirasvit Blog MX
* Added ability to set-up custom css styles in magento backend
* i18n

#### Fixed
* Fixed compatibility issue with Amasty_Shopby
* Fixed an issue with cache
* Fixed an issue related with autocomplete position on some devices

---

## 1.0.7
*(2016-02-22)*

#### Fixed
* Fixed an issue related with case sensitive search results (should be same for both registers)
* Fixed an bug with undefined configuration for search index
* Cache id for results

---


## 1.0.6
*(2016-02-15)*

#### Improvements
* Added caching for results (tag FULL Page Cache)
* Added link/url for Popular Suggestions
* Changed autocomplete block (added injection). Removed form.
* Added form loaded state

#### Fixed
* Fixed issue with broken product image url, if image not assigned to image
* Fixed issues related with autocomlete injection
* Fixed an issue with page cache (increased TTFB)
* Fixed an performance issue related with locale/currency (temporary fix)

#### Documentation
* Added user manual

---

## 1.0.5
*(2016-02-02)*

#### Fixed
* Fixed an performance issue related with locale/currency (temporary fix)

#### Improvements
* Added form loaded state

---




# Search Spell Correction
## 1.0.7
*(2016-06-24)*

#### Fixed
* Compatibility with Magento 2.1

---

## 1.0.6
*(2016-06-16)*

#### Fixed
* Fixed an issue with changing index mode for misspell index

---

## 1.0.5
*(2016-04-27)*

#### Improvements
* Improved extension performance
* i18n

#### Documentation
* Updated installation steps

---

## 1.0.4
*(2016-02-23)*

#### Fixed
* Fixed an issue with segmentation fault during reindex (PHP7)

---

## 1.0.3
*(2016-02-07)*

#### Documentation
* Added user manual

---
