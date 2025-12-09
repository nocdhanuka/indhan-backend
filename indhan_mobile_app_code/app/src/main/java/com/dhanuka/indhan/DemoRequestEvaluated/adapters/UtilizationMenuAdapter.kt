package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import android.os.Bundle
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.fragment.app.FragmentStatePagerAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.activities.DemoUtilizationSummaryActivity
import com.dhanuka.indhan.DemoRequestEvaluated.fragments.OverviewFragment
import com.dhanuka.indhan.DemoRequestEvaluated.fragments.UtilizationSummaryList
import com.google.android.material.tabs.TabLayout

class UtilizationMenuAdapter(
    context: DemoUtilizationSummaryActivity,
    supportFragmentManager: FragmentManager,
    tabLayout: TabLayout
) :
    FragmentStatePagerAdapter(supportFragmentManager) {

    var numOfTabs = 2

    var tabLayout: TabLayout
    var context: Context

    //This will contain your Fragment references:
    var fragments = arrayOfNulls<Fragment>(numOfTabs)

    init {
        this.tabLayout = tabLayout
        this.context = context
    }


    override fun getItem(position: Int): Fragment {
        var fragment: Fragment? = null
        when (position) {
            0 -> {
                fragment = OverviewFragment()
            }
            1 -> {
                fragment = UtilizationSummaryList()
            }
        }

        if (fragment != null) {
            val bundle = Bundle()
            fragment.arguments = bundle
        }
        return fragment!!

    }

    //This populates your Fragment reference array:
    override fun instantiateItem(container: ViewGroup, position: Int): Any {
        val createdFragment = super.instantiateItem(container, position) as Fragment
        fragments[position] = createdFragment
        return createdFragment
    }

    override fun destroyItem(container: View, position: Int, `object`: Any) {
        super.destroyItem(container, position, `object`)
    }


    override fun getPageTitle(position: Int): CharSequence {

        var title = ""
        when (position) {
            0 -> {
                title = "Overview"
            }
            1 -> {
                title = "Utilization Summary"
            }
        }

        return title
    }

    override fun getCount(): Int {
        return numOfTabs
    }
}