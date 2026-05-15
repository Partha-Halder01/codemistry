import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { useAnalytics } from './hooks/useAnalytics';
import Layout from './components/Layout';
import ProtectedRoute from './components/ProtectedRoute';
import AdminLayout from './components/AdminLayout';
import Home from './pages/Home';
import Services from './pages/Services';
import ServiceDetails from './pages/ServiceDetails';
import About from './pages/About';
import Contact from './pages/Contact';
import Login from './pages/Login';
import Register from './pages/Register';
import Dashboard from './pages/Dashboard';
import AiSupport from './pages/AiSupport';
import Blog from './pages/Blog';
import BlogPost from './pages/BlogPost';

import ScrollToTop from './components/ScrollToTop';

// Admin Pages
import AdminDashboard from './pages/admin/AdminDashboard';
import ManageServices from './pages/admin/ManageServices';
import ManageMessages from './pages/admin/ManageMessages';
import ManageKnowledgeBase from './pages/admin/ManageKnowledgeBase';
import AdminAnalytics from './pages/admin/AdminAnalytics';
import ManageAiChats from './pages/admin/ManageAiChats';
import ManageBlogPosts from './pages/admin/ManageBlogPosts';

const AnalyticsTracker = () => {
  useAnalytics();
  return null;
};

function App() {
  return (
    <Router>
      <ScrollToTop />
      <AnalyticsTracker />
      <Routes>
        {/* Public Routes */}
        <Route path="/login" element={<Login />} />

        {/* Public Routes with Layout */}
        <Route path="/" element={<Layout />}>
          <Route index element={<Home />} />
          <Route path="services" element={<Services />} />
          <Route path="services/:slug" element={<ServiceDetails />} />
          <Route path="about" element={<About />} />
          <Route path="contact" element={<Contact />} />
          <Route path="ai-support" element={<AiSupport />} />
          <Route path="blog" element={<Blog />} />
          <Route path="blog/:slug" element={<BlogPost />} />

          {/* Protected User Routes */}
          <Route element={<ProtectedRoute />}>
            <Route path="dashboard" element={<Dashboard />} />
          </Route>
        </Route>

        {/* Protected Admin Routes */}
        <Route element={<ProtectedRoute allowedRoles={['admin']} />}>
          <Route path="/admin" element={<AdminLayout />}>
            <Route path="dashboard" element={<AdminDashboard />} />
            <Route path="analytics" element={<AdminAnalytics />} />
            <Route path="services" element={<ManageServices />} />
            <Route path="messages" element={<ManageMessages />} />
            <Route path="knowledge-base" element={<ManageKnowledgeBase />} />
            <Route path="ai-chats" element={<ManageAiChats />} />
            <Route path="blog-posts" element={<ManageBlogPosts />} />
          </Route>
        </Route>
      </Routes>
    </Router>
  );
}

export default App;
