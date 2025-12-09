package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp

import android.content.Context
import android.os.Bundle
import com.google.android.material.tabs.TabLayout
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.fragment.app.FragmentStatePagerAdapter
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.ATLListFragment
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.ActiveTicketFragment
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.HistoryFragment
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.MyATPFragment

class SupervisorATPAdapter(context: Context, fm: FragmentManager, tabLayout: TabLayout) :
    FragmentStatePagerAdapter(fm) {
    var numOfTabs = 4

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
                fragment = ATLListFragment()
            }
            1 -> {
                fragment = MyATPFragment()
            }
            2 -> {
                fragment = ActiveTicketFragment()
            }
            3 -> {
                fragment = HistoryFragment()
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

        var title = ""
        when (position) {
            0 -> {
                title = "ATP List"
            }
            1 -> {
                title = "My ATPs"
            }
            2 -> {
                title = "Active Tickets"
            }
            3 -> {
                title = "History"
            }

        }

        return title
    }

    override fun getCount(): Int {
        return numOfTabs
    }

}