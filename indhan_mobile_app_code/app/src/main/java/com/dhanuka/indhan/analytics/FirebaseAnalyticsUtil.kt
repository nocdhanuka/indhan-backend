package com.dhanuka.indhan.analytics

import android.app.Activity
import android.content.Context
import android.os.Bundle
import com.dhanuka.indhan.BuildConfig
import com.google.firebase.analytics.FirebaseAnalytics

/**
 * A tracker Util class to send app events to Firebase analytics
 * Please call FirebaseTracker.initialize(context) in the Application class before calling any other function
 */
object FirebaseAnalyticsUtil {

    private lateinit var firebaseAnalytics: FirebaseAnalytics

    @JvmStatic
    fun initialize(context: Context) {
        firebaseAnalytics = FirebaseAnalytics.getInstance(context)
    }

    fun hitEvent(action: String, block: EventParamsBuilder.() -> EventParamsBuilder) {
        if (!BuildConfig.DEBUG) {
            firebaseAnalytics.logEvent(action, EventParamsBuilder().block().build())
        }
    }

    interface TrackableScreen {
        fun getTrackingActivity(): Activity?
    }

    class EventParamsBuilder {

        private val bundle = Bundle()

        fun category(category: String) = apply {
            bundle.putString(FirebaseAnalytics.Param.ITEM_CATEGORY, category)
        }

        fun labelAndValue(label: String, value: Any?) = apply {
            when (value) {
                is Long -> bundle.putLong(label, value)
                is Boolean -> bundle.putBoolean(label, value)
                is String -> bundle.putString(label, value)
            }
        }

        fun build() = bundle
    }
}
