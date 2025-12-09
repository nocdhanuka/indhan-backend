package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_bdo_plan_ml_new.view.*

//

class S1Adapter(homeCategoryRecordResponseModelArrayList: ArrayList<BDOFilterResponse.AreaManagerList>) :
    RecyclerView.Adapter<S1Adapter.MyViewHolder>() {

    private var filteredData: ArrayList<BDOFilterResponse.AreaManagerList>
    lateinit var onclickListener: AreaManagerAdapterListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onAMSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: AreaManagerAdapterListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_bdo_plan_ml_new, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.bdo_name_tv.text = plan.areaNAME
        /*holder.itemView.no_of_days_tv.visibility = View.GONE
        holder.itemView.date_tv.visibility = View.GONE*/
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface AreaManagerAdapterListener {
        fun onAMSelected(model: BDOFilterResponse.AreaManagerList)
    }
}
