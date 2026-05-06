# StackWise AI Project Overview

## What This Project Is

StackWise AI is a Laravel-based web application that helps users choose a suitable programming language, framework, and SDLC model for a software project. It is designed around student and beginner-friendly decision making, with a focus on clear explanations rather than a black-box answer.

The application currently uses a service-driven Laravel MVC structure. Its recommendation engine is rule-based for now, with data persistence, feedback collection, documentation browsing, and chatbot support already built into the project.

## Main Purpose

The core purpose of StackWise AI is to guide users through technology selection by considering project type, complexity, team size, preferred platform, development experience, timeline, and project goal. Instead of simply returning a stack, it also explains why that stack fits, what alternatives exist, what risks to expect, and what skills or roadmap items may still be needed.

## Key Features

### Recommendation Engine

The recommendation feature is the heart of the project. Users can submit project details and receive:

- A recommended programming language
- A recommended framework
- A recommended SDLC model
- A confidence score
- A written explanation for each choice
- Alternative stack suggestions
- Risk analysis
- Skill gap analysis
- A project roadmap

Recommendations are also stored in the database, which allows users to revisit history and review past results.

### Recommendation History

The app keeps saved recommendations and exposes a history view so users can browse previous submissions. Each record can also include feedback entries tied to the recommendation.

### Feedback Collection

Users can submit feedback with a rating and optional comment. This feedback is linked to recommendation records and can be used later for improving the recommendation experience or analytics.

### Documentation Explorer

The documentation section presents structured learning references for:

- Programming languages
- Development frameworks
- SDLC models

This gives the app a reference library that supports the recommendation flow and helps users understand why a stack was suggested.

### Chatbot

The chatbot currently uses a temporary rule-based response system. It can answer basic questions about:

- Python
- Laravel
- FastAPI
- Agile
- Waterfall
- SDLC
- StackWise itself

This is a placeholder for a more advanced AI-backed conversation flow later.

### Dashboard

The dashboard summarizes project activity with metrics such as:

- Total recommendations
- Average confidence score
- Most recommended language
- Most recommended framework
- Most recommended SDLC model
- Total feedback count
- Average feedback rating

## Application Pages

The main routes currently include:

- Home page
- Dashboard
- Recommendation generator
- Recommendation history
- Recommendation detail view
- Feedback submission
- Documentation explorer
- Chatbot
- About page

## How The App Is Structured

StackWise AI follows a clean Laravel MVC structure with a service layer handling the main business logic.

### Controllers

Controllers are responsible for receiving requests, coordinating validation, and passing work to services.

### Services

The service classes contain the project’s main behavior:

- `RecommendationService` builds stack suggestions, stores recommendation records, and formats saved recommendation reports.
- `DocumentationService` provides curated learning data for languages, frameworks, and SDLC models.
- `ChatbotService` generates simple intent-based responses.
- `FeedbackService` stores feedback safely, including validation of recommendation links.
- `DashboardService` aggregates statistics for the dashboard.

### Models

The main data models are:

- `Recommendation` for stored stack suggestions and project details
- `Feedback` for user ratings and comments
- `TechnologyDocument` for documentation explorer content

### Data Design

Recommendations use JSON fields for richer structured output, including explanations, alternatives, risks, skill gaps, and roadmap items. This makes the saved recommendation record more informative than a simple text result.

## Current Recommendation Logic

The current recommendation engine is not a live AI model. It uses rule-based logic that looks at project keywords and project context to detect likely use cases such as:

- Web applications
- AI or data-focused projects
- Mobile projects
- Real-time or chat-oriented systems

Based on the detected profile, the service recommends an appropriate language, framework, and SDLC model.

This approach keeps the application predictable and easy to understand, while leaving room for future AI integration later.

## Tech Stack

The project is built with:

- Laravel 13
- PHP 8.3
- Blade templates
- Tailwind CSS
- MySQL-backed persistence
- Pest for testing

## Future Direction

The current codebase already hints at future upgrades, including:

- AI-assisted recommendation scoring
- FastAPI integration for AI services
- Ollama-powered chatbot responses
- More advanced analytics from stored recommendation and feedback data

## Summary

StackWise AI is a structured recommendation platform for choosing a project stack and development process. It combines clear guidance, explainable outputs, saved history, feedback collection, documentation browsing, and a lightweight chatbot in one Laravel application.