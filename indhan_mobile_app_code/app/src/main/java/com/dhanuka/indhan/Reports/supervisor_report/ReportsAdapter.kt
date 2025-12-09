package com.dhanuka.indhan.Reports.supervisor_report

import android.os.Bundle
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.fragment.app.FragmentStatePagerAdapter
import android.view.ViewGroup
import com.dhanuka.indhan.Reports.supervisor_report.fragments.CustomerCareFragment
import com.dhanuka.indhan.Reports.supervisor_report.fragments.DemosFragment
import com.dhanuka.indhan.Reports.supervisor_report.fragments.EfficiencyFragment
import com.dhanuka.indhan.Reports.supervisor_report.fragments.TestimonialFragment


class ReportsAdapter(var fragmenrManager: FragmentManager, var data: SupervisorReportsBean.Data) :
    FragmentStatePagerAdapter(fragmenrManager) {


    internal var NumbOfTabs = 4
    // / Store the number of tabs, this will also be passed when the DashboardPagerAdapter is created

    //This will contain your Fragment references:
    var fragments = arrayOfNulls<Fragment>(NumbOfTabs)

    // Build a Constructor and assign the passed Values to appropriate values in the class


    //This method return the fragment for the every position in the View Pager
    override fun getItem(position: Int): Fragment {
        lateinit var fragment: Fragment
        var bundle = Bundle()
        bundle.putSerializable("data", data)
        when (position) {

            0 -> {
                fragment = EfficiencyFragment()
                fragment.arguments = bundle
            }
            1 -> {
                fragment = DemosFragment()
                fragment.arguments = bundle

            }
            2 -> {
                fragment = CustomerCareFragment()
                fragment.arguments = bundle

            }
            3 -> {
                fragment = TestimonialFragment()
                fragment.arguments = bundle

            }

        }

        return fragment

    }

    // This method return the titles for the Tabs in the Tab Strip

    override fun getPageTitle(position: Int): CharSequence {

        return ""
    }

    //This populates your Fragment reference array:
    override fun instantiateItem(container: ViewGroup, position: Int): Any {
        val createdFragment = super.instantiateItem(container, position) as Fragment
        fragments[position] = createdFragment
        return createdFragment
    }

    override fun destroyItem(container: ViewGroup, position: Int, `object`: Any) {
        super.destroyItem(container, position, `object`)
    }

    // This method return the Number of tabs for the tabs Strip
    override fun getCount(): Int {
        return NumbOfTabs
    }
}