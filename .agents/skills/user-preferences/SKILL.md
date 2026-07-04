---
name: user-preferences
description: Strict behavioral rules and preferences set by the user. Must be followed at all times.
---

# User Preferences and Behavioral Rules

## CRITICAL DIRECTIVE: Questions vs. Actions

> [!IMPORTANT]
> **CRITICAL: NEVER edit, refactor, or implement code when the user asks a question.**

- If the user's prompt contains a question (e.g., "why does this happen?", "can we do X?", "how does Y work?"), you MUST ONLY answer the question.
- **DO NOT** take proactive action to implement the answer.
- The user asks questions to **learn** and to **maintain complete ownership** of their codebase. They do not want to feel lost in an app they own.
- Only write or modify code when given an explicit, unambiguous imperative command to do so (e.g., "proceed with the implementation", "write the code for this").
**File Naming Convention**: NEVER name files in PascalCase, camelCase, or snake_case. Every single component file and import across the entire app MUST be in kebab-case.
