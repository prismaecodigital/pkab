@component('mail::message')
# New PKAB Created

A new PKAB has been created with the following details:

- ID: {{ $pkab->id }}
- Name: {{ $pkab->name }}
- Status: {{ $pkab->status }}

@endcomponent
