package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_cc_rq_list_row.view.*


class CCRequestListAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<CCQueriesForS1Model.ResponseList>) :
    RecyclerView.Adapter<CCRequestListAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<CCQueriesForS1Model.ResponseList>
    var record_bck: ArrayList<CCQueriesForS1Model.ResponseList>? = ArrayList()
    // lateinit var onclickListener: CCListClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
        this.record_bck?.addAll(homeCategoryRecordResponseModelArrayList)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            /*view.setOnClickListener { view ->
                if ((filteredData != null) && (filteredData.size > adapterPosition) && (adapterPosition != -1)) {
                    onclickListener.onSelected(filteredData[adapterPosition])
                }
            }*/
        }
    }

//    fun setClickListener(onclickListener: CCListClickListener) {
    //  this.onclickListener = onclickListener
    //  }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_cc_rq_list_row, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.bdo_name.text = "BDO Name :" + plan.name
        holder.itemView.time_tv.text = plan.createdAt
        if (!plan.product.isNullOrEmpty()) {
            holder.itemView.prod_type_tv.visibility = View.VISIBLE
            holder.itemView.prod_type_tv.text = plan.product
        } else
            holder.itemView.prod_type_tv.visibility = View.GONE

        holder.itemView.remark_tv.text = plan.remark

    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface CCListClickListener {
        fun onSelected(model: CCQueriesForS1Model.ResponseList)
    }

}
