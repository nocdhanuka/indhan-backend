package com.dhanuka.indhan.MyLeadMenu.adapters

import android.content.Context
import android.os.Bundle
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.fragment.app.FragmentPagerAdapter
import com.dhanuka.indhan.MyLeadMenu.fragments.ClosedLeadsFragment.ClosedLeadsFragment
import com.dhanuka.indhan.MyLeadMenu.fragments.OpenLeadsFragment.OpenLeadsFragment
import com.dhanuka.indhan.R

class MyLeadPagerAdapter(context: Context, fm: FragmentManager, var ddId: String) :
    FragmentPagerAdapter(fm) {

    var tabTitles = arrayOf<String>(
        context.resources.getString(R.string.open_leads),
        context.resources.getString(R.string.closed_leads)
    )

    override fun getItem(position: Int): Fragment {
        val bundle = Bundle()
        bundle.putString("ddId", ddId)



        if (position == 0) {
            val fm = OpenLeadsFragment()
            fm.arguments = bundle
            return fm

        } else if (position == 1) {
            val fm = ClosedLeadsFragment()
            fm.arguments = bundle
            return fm

        } else {
            val fm = OpenLeadsFragment()
            fm.arguments = bundle
            return fm
        }

    }

    override fun getPageTitle(position: Int): CharSequence? {
        return tabTitles[position]
    }

    override fun getCount(): Int {
        return 2
    }

}