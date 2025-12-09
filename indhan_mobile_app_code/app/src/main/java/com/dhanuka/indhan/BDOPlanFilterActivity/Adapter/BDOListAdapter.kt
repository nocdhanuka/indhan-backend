package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_name.view.*

class BDOListAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<BDOFilterResponse.BDOList>) :
    RecyclerView.Adapter<BDOListAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<BDOFilterResponse.BDOList> =
        homeCategoryRecordResponseModelArrayList
    lateinit var onclickListener: BDOListAdapterListener

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onBDOSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: BDOListAdapterListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_name, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.name.text = plan.bdoNAME
        if (plan.isSelected)
            holder.itemView.selected.visibility = View.VISIBLE
        else
            holder.itemView.selected.visibility = View.GONE
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface BDOListAdapterListener {
        fun onBDOSelected(model: BDOFilterResponse.BDOList)
    }
}
