package com.dhanuka.indhan.Reports.supervisor_report.fragments


import android.os.Bundle
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup

import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.supervisor_report.DemosAdapter
import com.dhanuka.indhan.Reports.supervisor_report.SupervisorReportsBean
import kotlinx.android.synthetic.main.fragment_demos.*

/**
 * A simple [Fragment] subclass.
 */
class DemosFragment : Fragment() {

    private lateinit var data: SupervisorReportsBean.Data
    private lateinit var adapter: DemosAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment

        data = arguments?.getSerializable("data") as SupervisorReportsBean.Data





        return inflater.inflate(R.layout.fragment_demos, container, false)
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        adapter = DemosAdapter(requireContext(), data.demolist)
        recyclerViewDemos.layoutManager =
            LinearLayoutManager(requireContext())
        recyclerViewDemos.adapter = adapter

    }


}
