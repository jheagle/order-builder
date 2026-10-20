# Order Builder

## Summary Order Builder

This project is intended to demonstrate example usage of using laravel for a made up order builder task.

## Description

User Story: _As a customer, I would like to have all of my orders neatly sorted on a single sheet_

### Acceptance Criteria

* Be able to create orders

* Orders can be printed onto a sheet

* Items in an order will be arranged to fit the size of the sheet

### Test Plan

* Incoming orders must have order items

* Each order item matches a valid product

* A successful incoming order with items will be stored

* Print sheet request will generate print items for each order item

* Requesting a print sheet will return a correctly formed print sheet (print items are all present, no overflow)

* Orders must have all of their items on the same print sheet

* Automated coverage for 50 sheet configurations

### Developer Notes

* Sheets are a grid of 10 by 15 units (width by height)

* Available product sizes: 1x1, 2x2, 3x3, 4x4, 5x2, 2x5

* Orders can have any number of items with any number of quantity

* Must be able to track placement of products on the sheets

* Avoid wasting print sheet space
