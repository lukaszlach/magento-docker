## 1.0.34
*(2016-09-06)*

#### Improvements
* Prepare cms block during categories reindex
* Cms Index

#### Fixed
* Fixed an issue with multistore results + added redirect via native store switcher controller

---

## 1.0.32
*(2016-08-19)*

#### Improvements
* Ability to search by blocks content in cms pages

#### Fixed
* Fixed an sphinx issue related with attributes

---

## 1.0.31
*(2016-08-09)*

#### Fixed
* Fixed an issue with sphinx index attributes

---


## 1.0.30
*(2016-08-05)*

#### Fixed
* Fixed an issue with category index (multi-store configuration)

---

## 1.0.28
*(2016-07-07)*

#### Improvements
* Added pager to wordpress blog search results

#### Fixed
* Fixed an issue related with creating temporary table on external database (external wordpress blog)

---

## 1.0.27
*(2016-07-06)*

#### Fixed
* Fixed an issue with displaying inline blocks, when search by cms pages
* Search sphinx with 2.1
* Fixed an issue with multi-store configuration

---


## 1.0.26
*(2016-06-29)*

#### Fixed
* Fixed an issue with applying results limit on category page

---

## 1.0.25
*(2016-06-29)*

#### Improvements
* Added additional exceptions for 404 to redirect

---

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