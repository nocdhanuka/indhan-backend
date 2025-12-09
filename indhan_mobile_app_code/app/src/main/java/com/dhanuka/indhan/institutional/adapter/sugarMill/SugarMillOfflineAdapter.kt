package com.dhanuka.indhan.institutional.adapter.sugarMill


import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.sugarMill.AddSugarMillBranchModel
import kotlinx.android.synthetic.main.tea_estate_item_adapter.view.*
import java.util.*

class SugarMillOfflineAdapter(
    selfParticipationMOffline: ArrayList<AddSugarMillBranchModel>,
    private val mActivityContext: AppCompatActivity,
    var clickListener: OnClickListener
) : RecyclerView.Adapter<SugarMillOfflineAdapter.MyViewHolder>() {

    private val teaEstateList: List<AddSugarMillBranchModel>


    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    init {
        this.teaEstateList = selfParticipationMOffline
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.tea_estate_item_adapter, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(
        holder: MyViewHolder,
        position: Int
    ) {
        holder.itemView.tvCompanyName.text = "- ".plus(teaEstateList[position].name)
        holder.itemView.tvTypeData.text =
            "- ".plus(if (teaEstateList[position].sugarmill_type == 1) "Head Office" else "Branch Office")
        holder.itemView.tvDistrictData.text = "- ".plus(teaEstateList[position].district)
        holder.itemView.tvStateData.text = "- ".plus(teaEstateList[position].state)
        holder.itemView.tvCategoryData.text =
            "- ".plus(teaEstateList[position].sugarmill_category_type)
    }

    override fun getItemCount(): Int {
        return teaEstateList.size
    }
}
