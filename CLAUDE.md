# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

JBZoo Mermaid-PHP is a PHP library for generating Mermaid diagrams and flowcharts. It provides PHP objects to programmatically create various types of diagrams including Graph/Flowcharts, ER Diagrams, Class Diagrams, and Timeline diagrams, which are then rendered as Mermaid syntax or HTML.

## Development Commands

### Build and Dependencies
```bash
make update                    # Install/update all dependencies
composer update                # Alternative to make update
```

### Testing
```bash
make test                     # Run PHPUnit tests
make test-all                 # Run all tests and code style checks
phpunit                       # Run tests directly with PHPUnit
```

### Code Quality
```bash
make codestyle               # Run all code quality checks (linting, static analysis)
```

### Comprehensive Testing
```bash
make report-all              # Generate all reports (coverage, static analysis, etc.)
make report-coveralls        # Upload coverage to Coveralls
```

## Core Architecture

### Main Diagram Types
The library is organized around four main diagram types, each in its own namespace (30 total source files):

1. **Graph** (`src/Graph.php`) - Flowcharts and basic graphs with nodes and links
2. **ERDiagram** (`src/ERDiagram/`) - Entity-Relationship diagrams for database schemas
3. **ClassDiagram** (`src/ClassDiagram/`) - UML class diagrams with classes, relationships, namespaces, and cardinality
4. **Timeline** (`src/Timeline/`) - Timeline diagrams for chronological data

### Core Components

#### Rendering System
- **`Render`** (`src/Render.php`) - Central rendering engine that converts diagrams to HTML
- Supports multiple themes (default, forest, dark, neutral)
- Generates standalone HTML with embedded Mermaid.js
- Provides live editor URLs for debugging

#### Graph Components
- **`Node`** (`src/Node.php`) - Individual graph nodes with various shapes (square, circle, round)
- **`Link`** (`src/Link.php`) - Connections between nodes with optional labels
- **`Graph`** - Container for nodes, links, and subgraphs with ordering and styling options

#### ER Diagram Components
- **`Entity`** (`src/ERDiagram/Entity/Entity.php`) - Database entities with properties
- **`EntityProperty`** - Entity attributes with types, constraints, and descriptions
- **`Relation`** classes - Various relationship types (OneToOne, OneToMany, ManyToOne, ManyToMany)

#### Class Diagram Components
- **`Concept`** (`src/ClassDiagram/Concept/Concept.php`) - UML classes with attributes and methods
- **`Attribute`** / **`Method`** / **`Argument`** - Class members with visibility and type information
- **`ConceptNamespace`** - Namespace grouping for organizing classes
- **`Relationship`** - Class relationships with RelationType enum (inheritance, composition, etc.)
- **`Cardinality`** enum - Relationship cardinality (ONE, ZERO_OR_ONE, ONE_OR_MORE, MANY, etc.)
- **`Link`** enum - Link styles (SOLID, DASHED)
- **`Visibility`** enum - Member visibility (PUBLIC, PRIVATE, PROTECTED, PACKAGE)

#### Timeline Components
- **`Timeline`** - Container for timeline sections and markers
- **`Marker`** - Time points with associated events
- **`Event`** - Individual timeline events

### Common Patterns

#### Fluent Interface
All diagram classes use method chaining for building:
```php
$graph = (new Graph(['title' => 'My Graph']))
    ->addNode($nodeA)
    ->addNode($nodeB)
    ->addLink(new Link($nodeA, $nodeB));
```

#### Rendering Options
All diagram types support:
- `__toString()` - Returns Mermaid syntax
- `renderHtml($options)` - Returns complete HTML with Mermaid.js
- `getLiveEditorUrl()` - Returns URL to Mermaid live editor

#### Helper Utilities
- **`Helper`** (`src/Helper.php`) - String escaping and formatting utilities for Mermaid syntax
- **`Direction`** (`src/Direction.php`) - Enum for diagram directions (TOP_TO_BOTTOM, BOTTOM_TOP, LEFT_RIGHT, RIGHT_LEFT)
- **`Exception`** (`src/Exception.php`) - Base exception class for library-specific errors
- **Timeline exceptions** - Specialized exceptions for timeline validation (e.g., SectionHasNoTitleException)

### Testing Structure
- Tests follow PSR-4 autoloading in `tests/` directory (7 test files)
- Test namespace: `JBZoo\PHPUnit\`
- **Test coverage**: ClassDiagramTest, ERDiagramTest, FlowchartTest, TimelineTest
- **Utility tests**: MermaidPackageTest, MermaidPhpStormProxyTest
- All tests extend custom PHPUnit base class with helper methods
- PHPUnit configuration in `phpunit.xml.dist`
- Tests include HTML output generation for visual verification

### Dependencies
- **Runtime**: PHP 8.2+, ext-json
- **Development**: jbzoo/toolbox-dev (includes PHPUnit, code style tools, etc.)
- Uses JBZoo ecosystem tools for consistent development experience