package com.dhanuka.indhan.analytics

class FATracker : Tracker<FAEvent> {
    override fun track(event: FAEvent) {
        FirebaseAnalyticsUtil.hitEvent(event.name ?: "Null Event") {
            category(event.category ?: "Null event")
            event.attrs?.forEach { (key, value) -> labelAndValue(key, value) }
            this
        }
    }
}
