package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import android.os.Bundle
import com.google.android.material.tabs.TabLayout
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.fragment.app.FragmentStatePagerAdapter
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.DemoRequestEvaluated.fragments.AwaitedFragment
import com.dhanuka.indhan.DemoRequestEvaluated.fragments.EvaluatedFragment
import com.dhanuka.indhan.DemoRequestEvaluated.fragments.OverdueFragment
import com.dhanuka.indhan.R

class DemoRequestEvaluatedAdapter(context: Context, fm: FragmentManager, tabLayout: TabLayout) :
    FragmentStatePagerAdapter(fm) {
    var numOfTabs = 3

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
                tabLayout.getTabAt(0)?.setCustomView(R.layout.tab_evaluated)
                fragment = EvaluatedFragment()
            }
            1 -> {
                tabLayout.getTabAt(0)?.setCustomView(R.layout.tab_awaited)
                fragment = AwaitedFragment()
            }
            2 -> {
                tabLayout.getTabAt(0)?.setCustomView(R.layout.tab_overdue)
                fragment = OverdueFragment()
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


    override fun getPageTitle(position: Int): CharSequence? {
        return ""
    }

    override fun getCount(): Int {
        return numOfTabs
    }

}