package com.dhanuka.indhan.analytics

interface Tracker<E> {
    fun track(event: E)
}
