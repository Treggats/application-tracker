# LLM Council — methodiek

Gebaseerd op [Karpathy's LLM Council](https://github.com/karpathy/llm-council):
meerdere perspectieven reageren onafhankelijk op een vraag, evalueren elkaars
standpunten, en een voorzitter synthetiseert het geheel.

Gebruik de council voor beslissingen waarbij meerdere belangen tegen elkaar
afgewogen moeten worden: nieuwe use cases, architectuurkeuzes, prioritering,
of het heroverwegen van bewuste uitsluitingen.

---

## Vaste raadsleden

| Naam | Perspectief | Karakter |
|---|---|---|
| **Vera** | Veiligheid & Controle | Bewaakt data-integriteit en risico's. Stelt de vraag: wat kan er misgaan, en wie draagt de gevolgen? |
| **Gijs** | Gebruiksgemak | Spreekt namens de eindgebruiker (Antje). Stelt de vraag: wat kost dit aan flow en dagelijks gebruik? |
| **Lars** | Technische haalbaarheid | Beoordeelt implementeerbaarheid en ontwerp. Stelt de vraag: wat is er écht nodig om dit te bouwen? |
| **Bas** | Beheer & Governance | Bewaakt beheersbaarheid en auditeerbaarheid. Stelt de vraag: hoe houden we grip op wat er gebeurt? |

---

## Format (drie fasen)

### Fase 1 — Initiële standpunten
Elk raadslid reageert onafhankelijk op de vraag. Geen onderlinge afstemming.
Elk standpunt sluit af met de kernvraag van dat perspectief.

### Fase 2 — Evaluatie
Elk raadslid reageert op de standpunten van de anderen:
wat overtuigt, wat mist, waar zit spanning?

### Fase 3 — Synthese
De voorzitter (geen vast raadslid) integreert de deliberatie in een conclusie.
Standaardformaat: tabel met use case / advies / redenering.

---

## Template
Resultaat komt in .ai/council te staan.
```
# LLM Council — [onderwerp]

Vraag aan de raad: *[concrete vraag]*

---

## Fase 1 — Initiële standpunten

**Vera (Veiligheid & Controle)**
[standpunt]

**Gijs (Gebruiksgemak)**
[standpunt]

**Lars (Technische haalbaarheid)**
[standpunt]

**Bas (Beheer & Governance)**
[standpunt]

---

## Fase 2 — Evaluatie

**Vera over de anderen:** [reactie]
**Gijs over de anderen:** [reactie]
**Lars over de anderen:** [reactie]
**Bas over de anderen:** [reactie]

---

## Fase 3 — Synthese

| Onderwerp | Advies | Redenering |
|---|---|---|
| … | … | … |
```
