<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(44, 169, 189, 0.15);
        border-color: rgba(44, 169, 189, 0.35);
    }
    .welcome-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }
    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    .panel-card {
        background: #fff;
        border-radius: 20px;
        border: 2px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .panel-card-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        background: linear-gradient(to left, rgba(240, 249, 255, 0.6), rgba(255, 255, 255, 0.9));
    }
</style>
